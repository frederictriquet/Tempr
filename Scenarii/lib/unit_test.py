#!/usr/bin/env python
# -*- coding: UTF-8

from array import array
import traceback, sys, os


RED = "\033[31m"
GREEN = "\033[32m"
YELLOW = "\033[1;33m"
BLUE = "\033[34m"
NORMAL = "\033[0m"
def mystr(x):
    # print type(x)
    if type(x) in (unicode, str) :
        # print ":".join("{:02x}".format(ord(c)) for c in x)
        return x.encode('utf-8')
    return str(x)

class UnitTest:
    def __init__(self, restResponse, name, show_ok=False, show_ko=True, exit_on_fail=False):
        self.r = restResponse
        self.name = name
        self.show_ok = show_ok
        self.show_ko = show_ko
        self.exit_on_fail = exit_on_fail
        self.msg = []
        self.file = traceback.extract_stack()[-2][0][len(os.getcwd()):]
        self.line = traceback.extract_stack()[-2][1]
        self.success = True
        if restResponse.status_code == 500:
            show(restResponse)


    def expect_code(self, status):
        # if not self.success: return False
        return self.expect_value('status', status, self.r.status_code)

    def expect_headers_contain(self, fields):
        # if not self.success: return False
        res = True
        for k, v in fields.items():
            tmp = self.expect_header(k, v)
            if not tmp:
                res = False
        return res


    def expect_content_type(self, ct):
        # if not self.success: return False
        return self.expect_header('content-type', ct)

    def expect_content_type_json(self):
        # if not self.success: return False
        return self.expect_content_type('application/json')

    def expect_body_has_keys(self, fieldnames, exactly=False, path=[]):
        # if not self.success: return False
        res = True
        obj = self.get_body_value_from_path(path)
        try:
            for f in obj:
                if f in fieldnames:
                    fieldnames.remove(f)
                else:
                    if exactly:
                        self.msg.append(("unexpected field found: " + f, False))
                        self.success = False
                        res = False
        except TypeError:
            self.msg.append(("body is not iterable (not json?)", False))
            self.success = False
            return False
        if len(fieldnames) != 0:
            self.msg.append(("expected fields not found:", False))
            for f in fieldnames:
                self.msg.append(("    - " + f, False))
            self.success = False
            res = False
        else:
            self.msg.append(("object contains expected fields", True))
        return res

    def get_body_value(self, key):
        # if not self.success: return None
        # return self.r.json()[key]
        return self.get_body_value_from_path(path=[key])

    def expect(self, condition, msg):
        # if not self.success: return False
        if condition:
            self.msg.append((msg, True))
        else:
            self.msg.append((msg, False))
            self.success = False
        return condition

    def get_body_value_from_path(self, path=[]):
        # if not self.success: return False
        if not self.expect_content_type_json():
            return None
        cur = self.r.json()
        built_path = ''
        for p in path:
            built_path = built_path + ' / ' + mystr(p)
            p_exists = False
            # print type(p)
            # print type(cur)
            if type(cur) == list and type(p) == int:
                p_exists = (len(cur) > p)
            elif type(cur) == dict and type(p) == str:
                p_exists = (p in cur.keys())
            if not p_exists:
                self.msg.append((format("expected field not found at '%s': '%s'" % (built_path, mystr(p))), True))
                self.success = False
                return None
            cur = cur[p]
        return cur

    def expect_body_value(self, path, expected_value):
        return self.expect_value(path[-1], expected_value, self.get_body_value_from_path(path))

    def show(self):
        if self.success:
            print GREEN + 'TEST OK',
        else:
            print RED + 'TEST KO',
        print '{0:50}'.format('"' + self.name + '"') + self.file + ':' + mystr(self.line) + NORMAL
        for msg, isVerbose in self.msg:
            if isVerbose:
                if self.show_ok:
                    print GREEN, '\tOK', msg, NORMAL
            else:
                if self.show_ko:
                    print RED, '\tKO', msg, NORMAL
                    # show(self.r)
        if not self.success and self.exit_on_fail:
            print BLUE + 'EXITING DUE TO TEST FAILURE' + NORMAL
            sys.exit(1)


    def expect_array(self, var_name, array, size=None):
        # if not self.success: return False
        return self.expect_type(var_name, array, list) \
            and (size != None and self.expect_value("len(" + var_name + ")", size, len(array)))

    def expect_type(self, object_name, object, object_type):
        # if not self.success: return False

        return self.expect_value("type of " + object_name, object_type, type(object))


    def expect_value(self, value_name, expected_value, found_value):
        # if not self.success: return False
        if found_value == expected_value:
            self.msg.append((value_name + " is " + mystr(found_value) + " as expected", True))
            return True
        else:
            self.msg.append((value_name + " expected = " + mystr(expected_value) + ", found = " + mystr(found_value), False))
            self.success = False
            return False

    def expect_body(self, k, v):
        # if not self.success: return False
        return self.expect_value("body['" + k + "']", self.r.json()[k], v)

    def expect_header_exists(self, k):
        res = True
        if k not in self.r.headers.keys():
            self.msg.append(("headers['" + k + "'] expected", False))
            self.success = False
            res = False
        return res

    def expect_header(self, k, v):
        # if not self.success: return False
        # show(self.r)
        return self.expect_header_exists(k) and self.expect_value("headers['" + k + "']", v, self.r.headers[k])

