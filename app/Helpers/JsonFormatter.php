<?php

if (!function_exists('formatJson')) {

    /**
     * description
     *
     * @param
     * @return
     */
    function formatJson($message, $code = 200, $data = NULL)
    {
        return response()->json(compact('message', 'code', 'data'));
    }
}
