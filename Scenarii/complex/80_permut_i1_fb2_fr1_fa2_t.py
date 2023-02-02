#!/usr/bin/env python
# -*- coding: UTF-8

import md5, atexit, urllib, itertools

if 'LIBS' not in globals():  LIBS = '../lib'
execfile(LIBS + '/utils.py')
execfile(LIBS + '/unit_test.py')
execfile(LIBS + '/sub_tests.py')
execfile(LIBS + '/sub_tests2.py')

t=0

def is_valid(p, valid_suborders):
    for iso in valid_suborders:
        last_index = -1
        for e in iso:
            curr_index = p.index(e)
            if last_index > curr_index:
                return False
            last_index = curr_index
    return True

def end():
    #raw_input("Press Enter exit...")
    deleteUser(token1)
    deleteUser(token2)

def run(e):
    globals()[e]()

def iterpermut(actions, valid_suborders, which_one=None):
    permut = itertools.permutations(actions)
    if which_one is None:
        return enumerate(permut)
    return itertools.islice(enumerate(permut),which_one,which_one+1)

def run_multi(actions, valid_suborders):
    global t
    for p in iterpermut(actions, valid_suborders):
        if is_valid(list(p[1]), valid_suborders):
            print BLUE + str(p) + NORMAL
            t = t+1
            print t
            for e in p[1]:
                run(e)
            sleep(0.1)
            CF()
            end()

#atexit.register(end)

WS = get_ws_url()
email1 = "a@a.com"
email2 = "b@a.com"
pass1 = get_password()
fb_token2 = 'EAAVw7YpTBJQBAAsPd6BNwZB3BgozbExe81G0QDdMxiZBtaZAa1fd41CZBZCScqIsU4Pr5AuKC1TnuXjt8vmBNJcoj1ZBCVRxn75wbBtFqeFs6q5ZCMJY5u7JQZAZByOGvepEysRQlGafXKrBEoLJq7Uno8A12HfZA8ZBVOsHSsbmSBK7wZDZD'
fb_id2 = '101187593631747'
password = 'p4ssw0rd'

actions = [ 'I1','FBI2','FR1','FA2','TFB']
valid_suborders = [
                    ['I1','TFB'],
                    ['I1','FR1'],
                    ['I1','FA2'],
                    ['FBI2','FR1'],
                    ['FBI2','FA2'],
                    ['FR1','FA2']
                    ]
run_multi(actions, valid_suborders)


actions = [ 'I1','I2','FR1','FA2','FBC','TFB']
valid_suborders = [
                    ['I1','TFB'],
                    ['I2','FR1'],
                    ['I2','FA2'],
                    ['I1','FR1'],
                    ['I1','FA2'],
                    ['I2','FBC'],
                    ['FR1','FA2']
                    ]
run_multi(actions, valid_suborders)
