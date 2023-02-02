#!/usr/bin/env python
# -*- coding: UTF-8

import apnsclient

# con = Session().new_connection("feedback_sandbox", cert_file="push_dev.pem")

knuff = True
if knuff:
    cert_file = '/home/rodger/Synchro/Tempr/Certificats Apple/Knuff/push_knuff_cert.pem'
    key_file = '/home/rodger/Synchro/Tempr/Certificats Apple/Knuff/push_knuff_key_noenc.pem'
    token_hex = '89f77e4b139fb4888f7e8daeeaf69a10240316f2f3fd68d5c39ee0989ed935f5'
    use_sandbox = False
else:
    cert_file = '/home/rodger/Synchro/Tempr/Certificats Apple/push_dev_cert.pem'
    key_file = '/home/rodger/Synchro/Tempr/Certificats Apple/push_dev_key_noenc.pem'
    cert_file = '/home/rodger/Synchro/Tempr/Certificats Apple/21mars/push_dev_cert.pem'
    key_file = '/home/rodger/Synchro/Tempr/Certificats Apple/21mars/push_dev_key_noenc.pem'
    token_hex = 'CF29752F27FF3B5C8AAF6E21EC8E8CFE70CFAEE84856CD7BB1C9980AE9C5DF3B'
    token_hex = '095D23B22635ADB2442942EF6F6D915ABDD8756605DC0C507AC1E87BBE207B00'
    use_sandbox = True


if use_sandbox:
    env = 'push_sandbox'
else:
    env = 'push_production'

cert = apnsclient.certificate.BaseCertificate(cert_file=cert_file, key_file=key_file)
session = Session()
# con = session.get_connection("push_sandbox", cert_file=cert_file)
con = session.get_connection(env, certificate=cert)
# print(con)

message = Message(tokens=tokens, alert="Essai de push", badge=1)
print(message)

srv = APNs(con)
try:
    res = srv.send(message)
except:
    print "Can't connect to APNs, looks like network is down"
else:
    # Check failures. Check codes in APNs reference docs.
    for token, reason in res.failed.items():
        code, errmsg = reason
        # according to APNs protocol the token reported here
        # is garbage (invalid or empty), stop using and remove it.
        print "Device failed: {0}, reason: {1}".format(token, errmsg)

    # Check failures not related to devices.
    for code, errmsg in res.errors:
        print "Error: {}".format(errmsg)

    # Check if there are tokens that can be retried
    if res.needs_retry():
        # repeat with retry_message or reschedule your task
        retry_message = res.retry()
