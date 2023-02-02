#!/usr/bin/env python
# -*- coding: UTF-8

# ./generate_vars.py one local 172.16.100.10 32 > generated_vars/local_one.json
# ./generate_vars.py one preprod 10.133.2.41 32 > generated_vars/preprod_one.json
# ./generate_vars.py many local 172.16.100.  24 20 30 32 40 50 50 60 70 80 250 > generated_vars/local_multi.json
# ./generate_vars.py many prod 10.133.       16 17.59 17.135 17.163 17.180 17.201 17.201 17.205 18.119 18.128 17.203 > generated_vars/prod.json

from collections import OrderedDict
import json, sys
import yaml


hosts = ['ws', 'db', 'db2', 'www', 'dashboard', 'librenms', 'elasticsearch', 'jobs', 'store', 'gw']
hosts_fqdn = ['ws', 'www', 'dashboard', 'librenms', 'doc']
def build_hosts_one(ip):
    v = OrderedDict()
    v['hosts'] = []
    v['etchosts'] = []
    for h in hosts:
        v['hosts'].append({'hn': 'tempr_' + h + 'host_ip', 'ip': ip})
        v['etchosts'].append('tempr_' + h + 'host_ip')
    return v

def build_other_vars_one(subdomain, ip):
    if subdomain == 'local' or subdomain == 'preprod':
        subdomain = '.' + subdomain
    elif subdomain == 'prod':
        subdomain = ''
    v = OrderedDict()
    for h in hosts:
        v.update({'tempr_' + h + 'host_ip': ip})
    for h in hosts_fqdn:
        v.update({'tempr_' + h + 'host_fqdn': h + subdomain + '.tempr.me'})
    return v


def build_hosts_many(base_ip, ips):
    v = OrderedDict()
    v['hosts'] = []
    v['etchosts'] = []
    for i, h in enumerate(hosts):
        ip = base_ip + ips[i]
        v['hosts'].append({'hn': 'tempr_' + h + 'host_ip', 'ip': ip})
        v['etchosts'].append('tempr_' + h + 'host_ip')
    return v

def build_other_vars_many(subdomain, base_ip, ips):
    if subdomain == 'local' or subdomain == 'preprod':
        subdomain = '.' + subdomain
    elif subdomain == 'prod':
        subdomain = ''
    v = OrderedDict()
    for i, h in enumerate(hosts):
        ip = base_ip + ips[i]
        v.update({'tempr_' + h + 'host_ip': ip})
    for h in hosts_fqdn:
        v.update({'tempr_' + h + 'host_fqdn': h + subdomain + '.tempr.me'})
    return v


def build_aws_vars(subdomain):
    v = None
    with open('secret_vars/aws_' + subdomain + '.yml') as aws:
        v = yaml.load(aws)
    return v


v = OrderedDict()

if len(sys.argv) == 5 and sys.argv[1] == 'one':
    tempr_private_mask = sys.argv[4]
    v.update(build_hosts_one(sys.argv[3]))
    v.update(build_other_vars_one(sys.argv[2], sys.argv[3]))
elif len(sys.argv) > 6 and sys.argv[1] == 'many':
    tempr_private_mask = sys.argv[4]
    v.update(build_hosts_many(sys.argv[3],sys.argv[5:]))
    v.update(build_other_vars_many(sys.argv[2], sys.argv[3], sys.argv[5:]))
else:
    print("$0  one   local|preprod    ip  mask")
    print("$0  many   local|preprod|prod    base_ip mask ips")
    sys.exit()

if tempr_private_mask == "16":
    tempr_localnetwork_ip = sys.argv[3] + "0.0"
elif tempr_private_mask == "24":
    tempr_localnetwork_ip = sys.argv[3] + "0"
elif tempr_private_mask == "32":
    tempr_localnetwork_ip = sys.argv[3]
else:
    print("incorrect ip / mask")

v.update({'tempr_localnetwork_ip': tempr_localnetwork_ip})
v.update({'tempr_private_mask': tempr_private_mask})

if sys.argv[2] == 'prod': 
    v.update({'tempr_push_cert_pem': 'aps_prod_cert.pem'})
    v.update({'tempr_push_key_pem': 'aps_prod_key_noenc.pem'})
    v.update({'tempr_push_sandbox': False})
else:
    v.update({'tempr_push_cert_pem': 'push_dev_cert.pem'})
    v.update({'tempr_push_key_pem': 'push_dev_key_noenc.pem'})
    v.update({'tempr_push_sandbox': True})


v.update(build_aws_vars(sys.argv[2]))

print json.dumps(v, indent=4)
