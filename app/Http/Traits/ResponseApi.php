<?php
namespace App\Http\Traits;

trait ResponseApi
{

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendResponse($data, $messages = [], $code = 200)
    {
        if (!empty($messages)) {
            $messages = is_array($messages) ? $messages : [$messages];
        }
        $response = [

            'success' => true,

            'data' => $data,

            'messages' => $messages,

        ];

        return response()->json($response, $code);

    }

    /**

     * success response method.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendResponseStudent($data, $error , $messages = [], $arrayValidations, $code = 200)
    {
        if (!empty($messages)) {
            $messages = is_array($messages) ? $messages : [$messages];
        }
        $response = [

            'success' => true,

            'isBirthday' => $arrayValidations['isBirthday'],

            'hasAccess' => $arrayValidations['hasAccess'],

            'messages' => $messages,
        ];

        if (!empty($data)) {
            $response['student'] = $data;
        }

        if (!empty($error)) {
            $response['error'] = $error;
        }

        return response()->json($response, $code);

    }

    /**

     * return error response.

     *

     * @return \Illuminate\Http\Response

     */

    public function sendError($error, $messages = [], $code = 400)
    {
        if (!empty($messages)) {
            $messages = is_array($messages) ? $messages : [$messages];
        }

        $response = [

            'success' => false,

            'error' => $error,

            'messages' => $messages,
        ];

        return response()->json($response, $code);

    }

    // /**

    //  * return error response.

    //  *

    //  * @return \Illuminate\Http\Response

    //  */

    // public function sendErrorStudent($error, $messages = [], $arrayValidations, $code = 400, $student = null)
    // {
    //     if (!empty($messages)) {
    //         $messages = is_array($messages) ? $messages : [$messages];
    //     }

    //     $response = [

    //         'success' => false,

    //         'error' => $error,

    //         'messages' => $messages,

    //         'isBirthday' => isset($arrayValidations['isBirthday']) ? true : false,

    //         'isValidLocation' => isset($arrayValidations['isValidLocation']) ? true : false,

    //         'isValidClass' => isset($arrayValidations['isValidClass']) ? true : false,

    //         'isValidTimeClass' => isset($arrayValidations['isValidTimeClass']) ? true : false,

    //         'hasAccess' => isset($arrayValidations['hasAccess']) ? true : false,

    //         'isMatcherError' => isset($arrayValidations['isMatcherError']) ? true : false,
    //     ];

    //     if (!empty($student)) {
    //         $response['student'] = $student;
    //     }


    //     return response()->json($response, $code);

    // }
}
