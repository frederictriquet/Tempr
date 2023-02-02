#!/usr/bin/env python
# -*- coding: UTF-8

##################
# import time

import logging
import pika, json, requests, traceback
import sys

from tempr import conf
from tempr.log import Log
from tempr.pg_lib import *


def event_create(eventtype, from_id=None, to_id=None, post_id=None):
    # envoyer event à la bdd
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('event_create')
    cur = con.cursor()
    params = (eventtype, from_id, to_id, post_id)
    db_call(cur, 'event_create', params=params)
    cur.close()
    con.commit()
    con.close()

def event_remove(eventtype, from_id=None, to_id=None):
    # supprimer event de la bdd
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('event_remove')
    cur = con.cursor()
    params = (eventtype, from_id, to_id)
    db_call(cur, 'event_remove', params=params)
    cur.close()
    con.commit()
    con.close()

def create_push(eventtype, from_id=None, to_id=None, post_id=None):
    e = {'type': eventtype, 'from_id': from_id, 'to_id': to_id, 'post_id': post_id}
    pushchannel.basic_publish(exchange='',
                      routing_key='push',
                      body=json.dumps(e))

def create_sms(action, params):
    params['action'] = action
    #e = {'action': action, }
    smschannel.basic_publish(exchange='',
                      routing_key='sms',
                      body=json.dumps(params))




def flow_update_for_new_friendship(from_id, to_id):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('flow_update_for_new_friendship')
    cur = con.cursor()
    params = (from_id, to_id)
    db_call(cur, 'flow_update_for_new_friendship', params=params)
    cur.close()
    con.commit()
    con.close()

def flow_update_for_friendship_removal(from_id, to_id):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('flow_update_for_friendship_removal')
    cur = con.cursor()
    params = (from_id, to_id)
    db_call(cur, 'flow_update_for_friendship_removal', params=params)
    cur.close()
    con.commit()
    con.close()

def flow_update_for_new_post(user_id, post_id):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('flow_update_for_new_post')
    cur = con.cursor()
    params = (user_id, post_id)
    db_call(cur, 'flow_update_for_new_post', params=params)
    cur.close()
    con.commit()
    con.close()

def flow_update_for_post_removal(post_id):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('flow_update_for_post_removal')
    cur = con.cursor()
    params = (post_id,)
    db_call(cur, 'flow_update_for_post_removal', params=params)
    cur.close()
    con.commit()
    con.close()

def flow_update_for_user_removal(user_id):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('flow_update_for_user_removal')
    cur = con.cursor()
    params = (user_id,)
    db_call(cur, 'flow_update_for_user_removal', params=params)
    cur.close()
    con.commit()
    con.close()

def update_pending_posts_for_friendship(from_id, to_id):
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    log.debug('update_pending_posts_for_friendship '+str(from_id)+' -> '+str(to_id))
    cur = con.cursor()
    params = (from_id, to_id,)
    # mettre à jour les pending_posts destinés à to_id
    sql = 'select * from posts_unpend_for_friendship(%s, %s)'
    # et pour ceux qui ne sont plus pending, on continue le workflow
    db_events = db_execute(cur, sql, params=params)
    cur.close()
    con.commit()
    con.close()
    log.debug('******* before the loop')
    log.debug('******* '+str(db_events))
    for db_event in db_events:
        log.debug(str(db_event))
        # TODO voir pourquoi on récupère un tuple du genre ('post', 1L, 0L) au lieu d'un event normal
        e = {'type': 'post', 'post_id': db_event[1]}
        post(e)
    log.debug('******* after the loop')




################################################################################ LES FONCTIONS POUR CHAQUE EVENT

def friendship_request(e):
    log.debug('A FRIENDSHIP REQUEST '+str(e))
    from_id = int(e['from_id'])
    to_id = int(e['to_id'])
    event_create('friendship_request', from_id=from_id, to_id=to_id)
    create_push('friendship_request', from_id=from_id, to_id=to_id)

def friendship_acceptance(e):
    from_id = int(e['from_id'])
    to_id = int(e['to_id'])
    event_remove('friendship_request', from_id=to_id, to_id=from_id)
    event_create('friendship_acceptance', from_id=from_id, to_id=to_id)
    create_push('friendship_acceptance', from_id=from_id, to_id=to_id)
#     update_pending_posts_for_friendship(from_id=from_id, to_id=to_id)
    update_pending_posts_for_friendship(from_id=to_id, to_id=from_id)
    flow_update_for_new_friendship(from_id, to_id)

def friendship_removal(e):
    from_id = int(e['from_id'])
    to_id = int(e['to_id'])
    flow_update_for_friendship_removal(from_id, to_id)

def friendship_refusal(e):
    from_id = int(e['from_id'])
    to_id = int(e['to_id'])
    event_remove('friendship_request', from_id=from_id, to_id=to_id)

def post(e):
    log.debug(str(e))
    post_id = int(e['post_id'])
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    posts = db_retrieve(con,
        'select from_fk_user_id, to_fk_user_id from posts where pk_post_id = %s',
        (post_id,))
    if len(posts) == 1:
        log.debug(str(posts))
        from_id = posts[0][0]
        to_id = posts[0][1]
        flow_update_for_new_post(from_id, post_id)
        if from_id != to_id:
            event_create('post', from_id=from_id, to_id=to_id, post_id=post_id)
            flow_update_for_new_post(to_id, post_id);
            create_push(eventtype='post', from_id=from_id, to_id=to_id, post_id=post_id)
    con.close()

def post_delete(e):
    post_id = int(e['post_id'])
    flow_update_for_post_removal(post_id)

def like(e):
    from_id = int(e['from_id'])
    post_id = int(e['post_id'])
    # TODO ne rien faire si c'est moi qui like
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    posts = db_retrieve(con,
        'select from_fk_user_id, to_fk_user_id from posts where pk_post_id = %s',
        (post_id,))
    if len(posts) == 1:
        post_from_id = int(posts[0][0])
        post_to_id = int(posts[0][1])
        # envoyer notif au destinataire du post
        # log.debug(str(type(from_id)) + '  ' + str(type(post_to_id)))
        if from_id != post_to_id:
            event_create('i_am_liked', to_id=post_to_id, post_id=post_id)
            create_push(eventtype='i_am_liked', to_id=post_to_id, post_id=post_id)
        # envoyer notif au créateur du post, si différent du destinataire
        if post_from_id != post_to_id and from_id != post_from_id:
            log.debug(str(post_from_id) +' != '+str(post_to_id)+' and '+str(from_id)+' != '+str(post_from_id))
            event_create('they_like_it', to_id=post_from_id, post_id=post_id)
            create_push(eventtype='they_like_it', to_id=post_from_id, post_id=post_id)
    con.close()

def comment(e):
    from_id = int(e['from_id'])
    post_id = int(e['post_id'])
    # from_id c'est le créateur du commentaire
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    posts = db_retrieve(con,
        'select from_fk_user_id, to_fk_user_id from posts where pk_post_id = %s',
        (post_id,))
    if len(posts) == 1:
        post_from_id = int(posts[0][0])
        post_to_id = int(posts[0][1])

        # log.debug(str(type(from_id)) + '  ' + str(type(post_to_id)))
        dest = set()

        # envoyer notif au destinataire du post
        if from_id != post_to_id:
            dest.add(post_to_id)
        # envoyer notif au créateur du post, si différent du destinataire
        if post_from_id != post_to_id and from_id != post_from_id:
            dest.add(post_from_id)

        # envoyer notif aux autres auteurs de commentaires
        comments = db_retrieve(con,
            'select distinct from_fk_user_id from comments where fk_post_id = %s and from_fk_user_id != %s',
            (post_id,from_id))
        for c in comments:
            dest.add(c[0])

        log.debug(str(dest))
        for to_id in dest:
            event_create('comment', from_id=from_id, post_id=post_id, to_id=to_id)
            create_push('comment', from_id=from_id, post_id=post_id, to_id=to_id)
    con.close()

def user_delete(e):
    user_id = int(e['to_id'])
    flow_update_for_user_removal(user_id)

def stats_updated(e):
    user_id = int(e['to_id'])
    event_create('stats_updated', to_id=user_id)
    create_push('stats_updated', to_id=user_id)

def profile_updated(e):
    user_id = int(e['from_id'])
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    friends = db_retrieve(con,
        'select fk_user_id2 from friendships where fk_user_id1 = %s',
        (user_id,))
    for f in friends:
        event_create('profile_updated', from_id=user_id, to_id=f[0])

def background_updated(e):
    user_id = int(e['from_id'])
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    friends = db_retrieve(con,
        'select fk_user_id2 from friendships where fk_user_id1 = %s',
        (user_id,))
    for f in friends:
        event_create('background_updated', from_id=user_id, to_id=f[0])

def phone_confirmed(e):
    log.debug('PHONE CONFIRMATION')
    user_id = int(e['from_id'])
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    cur = con.cursor()

    sql = 'select * from user_unconfirm_other_phones(%s)'
    db_execute(cur, sql, params=(user_id,))

    sql = 'select * from posts_unpend_rcpt_by_phone(%s)'
    friendship_requests = db_execute(cur, sql, params=(user_id,))
    cur.close()
    con.commit()
    con.close()
    log.debug(friendship_requests)
    for fr in friendship_requests:
        e = {'type': fr[0], 'from_id': fr[1], 'to_id': fr[2]}
        if fr[0] == 'friendship_request':
            friendship_request(e)
        elif fr[0] == 'already_friends':
            update_pending_posts_for_friendship(fr[1], fr[2])
        elif fr[0] == 'friendship_acceptance':
            friendship_acceptance({'type': fr[0], 'from_id': fr[2], 'to_id': fr[1]})
        else:
            log.debug(fr)
    log.debug('END OF PHONE CONFIRMATION')

def fb_connected(e):
    log.debug('FB CONNECT')
    user_id = int(e['from_id'])
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    cur = con.cursor()
    sql = 'select * from posts_unpend_rcpt_by_fb(%s)'
    friendship_requests = db_execute(cur, sql, params=(user_id,))
    cur.close()
    con.commit()
    con.close()
    log.debug(friendship_requests)
    for fr in friendship_requests:
        e = {'type': fr[0], 'from_id': fr[1], 'to_id': fr[2]}
        if fr[0] == 'friendship_request':
            friendship_request(e)
        elif fr[0] == 'already_friends':
            update_pending_posts_for_friendship(fr[1], fr[2])
        elif fr[0] == 'friendship_acceptance':
            friendship_acceptance({'type': fr[0], 'from_id': fr[2], 'to_id': fr[1]})
        else:
            log.debug(fr)
    log.debug('END OF FACEBOOK CONFIRMATION')

def user_deleted(e):
    user_id = int(e['from_id'])
    log.debug("TODO supprimer les posts, purger les flux")
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    cur = con.cursor()
    sql = 'delete from users where pk_user_id = %s'
    db_execute(cur, sql, params=(user_id,))
    cur.close()
    con.commit()
    con.close()

def post_by_phone(e):
    post_id = int(e['post_id'])
    con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    # TOTO utiliser la vue
#     sql = 'select u.firstname, h.tag, pu.phone from pending_posts pp ' +  \
#         'join pending_users pu on pu.pk_user_id=pp.to_fk_pending_user_id ' + \
#         'join posts_htags_pending php on php.fk_post_id=pp.pk_post_id and ck_seq_id=1 ' + \
#         'join htags h on h.pk_htag_id=php.fk_htag_id ' + \
#         'join users u on u.pk_user_id=pp.from_fk_user_id ' + \
#         'where pp.pk_post_id = %s'
    sql = 'select from_firstname, tag1, phone from view_pending_posts_by_sms ' +  \
        'where pk_post_id = %s'
    info = db_retrieve(con, sql, params=(post_id,))
    log.debug(info)
    if len(info)==1:
        p = {'firstname': info[0][0], 'tag': info[0][1], 'phone': info[0][2], 'post_id': post_id}
        create_sms('post', p)
        sql = 'update pending_posts set nb_reminds = nb_reminds + 1, last_remind_date = current_date where pk_post_id = %s'
        cur = con.cursor()
        db_execute(cur, sql, params=(post_id,))
        cur.close()
        con.commit()
    con.close()

def new_user(e):
    #return # pour l'instant on ne le fait pas automatiquement
    event = { 'type': 'new_user', 'user_id': int(e['from_id'])}
    tempschannel.basic_publish(exchange='',
                      routing_key='temps',
                      body=json.dumps(event))
    
################################################################################################################


def callback(ch, method, properties, body):
    try:
        log.debug(" [x] Received %r" % body)
        try:
            event = json.loads(body)
        except ValueError:
            log.debug("Not JSON")
            return
        event_types = {
                       'friendship_request': friendship_request,
                       'friendship_acceptance': friendship_acceptance,
                       'friendship_removal': friendship_removal,
                       'friendship_refusal': friendship_refusal,
                       'post': post,
                       'post_delete': post_delete,
                       'like': like,
                       'comment': comment,
                       'user_delete': user_delete,
                       'stats_updated': stats_updated,
                       'profile_updated': profile_updated,
                       'background_updated': background_updated,
                       'phone_confirmed': phone_confirmed,
                       'fb_connected': fb_connected,
                       'user_deleted': user_deleted,
                       'post_by_phone': post_by_phone,
                       'new_user': new_user
                       }
        if event_types.has_key(event['type']):
            log.debug('EXEC '+str(event))
            event_types[event['type']]( event )
        else:
            log.debug("Unknown event type")
    except:
        raise
try:
    logging.getLogger("pika").setLevel(logging.WARNING)
    log = Log('eventprocessor', '/srv/Logs', global_level=logging.DEBUG, levels=(logging.DEBUG, logging.ERROR))
    log.debug("BEGIN READING INTERNAL EVENTS *************")
    
    #con = db_connect(conf.TEMPR_DB_HOST, conf.TEMPR_DB_NAME, conf.TEMPR_DB_USER)
    
    cred = pika.credentials.PlainCredentials('lapin', 'lapin')
    connection = pika.BlockingConnection(pika.ConnectionParameters('localhost', credentials=cred))
    channel = connection.channel()
    channel.queue_declare(queue='events')
    channel.basic_consume(callback, queue='events', no_ack=True)
    
    mailchannel = connection.channel()
    mailchannel.queue_declare(queue='mail')
    smschannel = connection.channel()
    smschannel.queue_declare(queue='sms')
    pushchannel = connection.channel()
    pushchannel.queue_declare(queue='push')
    tempschannel = connection.channel()
    tempschannel.queue_declare(queue='temps')

    channel.start_consuming()
except Exception, e:
    exc_type, exc_value, exc_traceback = sys.exc_info()
    log.error(str(e))
    log.error(str(traceback.format_exception(exc_type, exc_value, exc_traceback)))
finally:
    log.debug("END READING INTERNAL EVENTS ****************")
