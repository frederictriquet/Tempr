#!/usr/bin/env python
# -*- coding: UTF-8

from apns import APNs, Frame, Payload
import time

knuff = False
if knuff:
    cert_file = '/home/rodger/Synchro/Tempr/Certificats Apple/Knuff/push_knuff_cert.pem'
    key_file = '/home/rodger/Synchro/Tempr/Certificats Apple/Knuff/push_knuff_key_noenc.pem'
    token_hex = '89f77e4b139fb4888f7e8daeeaf69a10240316f2f3fd68d5c39ee0989ed935f5'
    use_sandbox = False
else:
    cert_file = '/home/rodger/Synchro/Tempr/Certificats Apple/push_dev_cert.pem'
    key_file = '/home/rodger/Synchro/Tempr/Certificats Apple/push_dev_key_noenc.pem'

    cert_file = 'push_dev_cert.pem'
    key_file = 'push_dev_key_noenc.pem'

    # cert_file = '/home/rodger/Synchro/Tempr/Certificats Apple/21mars/push_dev_cert.pem'
    # key_file = '/home/rodger/Synchro/Tempr/Certificats Apple/21mars/push_dev_key_noenc.pem'
    # token_hex = 'CF29752F27FF3B5C8AAF6E21EC8E8CFE70CFAEE84856CD7BB1C9980AE9C5DF3B'
    token_hex = 'FEA5D50D2BF2F4A80F5AC3D61261F69622492005E5DC04BCE764465A6E0C8573'
    token_hex = '37EFE58AD542DD625D7FFB679C959901E235A6BA789AE3E2A40C84C956B29F3C'
    token_hex = '28C82B21FDA83FEFCDBD0FCFCD7FD1E9006DE3C6C806A75E866A5B69D136C844'
    use_sandbox = True





apns = APNs(use_sandbox=use_sandbox, cert_file=cert_file, key_file=key_file)
# Send a notification
payload = Payload(
                  alert="Tempr ca dechire",
                  custom={'Tempr':'ca dechire'},
                  sound="default",
                  badge=1)
apns.gateway_server.send_notification(token_hex, payload)

# New APNS connection
feedback_connection = APNs(use_sandbox=use_sandbox, cert_file=cert_file, key_file=key_file)

# Get feedback messages.
for (token_hex, fail_time) in feedback_connection.feedback_server.items():
    # do stuff with token_hex and fail_time
    print(token_hex, fail_time)
