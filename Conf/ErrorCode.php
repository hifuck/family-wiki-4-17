<?php

/**
 * 错误码
 * author: jiangpengfei
 * date: 2018-04-03
 */

namespace Conf;

class ErrorCode {
    const ERROR_SUCCESS = '0';
    const ERROR_SQL_INIT = '-10000001';
    const ERROR_SQL_INSERT = '-10000002';
    const ERROR_SQL_DELETE = '-10000003';
    const ERROR_SQL_UPDATE = '-10000004';
    const ERROR_SQL_QUERY = '-10000005';
    const ERROR_LOGIN = '-20000001';
    const ERROR_PARAM_MISSING = '-20000002';
    const ERROR_PERMISSION = '-20000003';
    const ERROR_REQUEST = '-20000004';
    const ERROR_REGISTER_DUPLICATEEMAIL = '-20000006';
    const ERROR_REGISTER = '-20000007';
    const ERROR_INPUT_FORMAT = '-20000013';
    const ERROR_PARAM_WRONG = '-20000018';
}