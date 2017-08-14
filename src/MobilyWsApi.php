<?php

namespace NotificationChannels\MobilyWs;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\RequestException;
use NotificationChannels\MobilyWs\Exceptions\CouldNotSendNotification;

class MobilyWsApi
{
    /** @var MobilyWsConfig */
    private $config;

    /** @var HttpClient */
    private $http;

    /**
     * Create a new MobilyWs channel instance.
     *
     * @param MobilyWsConfig $config
     * @param HttpClient     $http
     */
    public function __construct(MobilyWsConfig $config, HttpClient $http)
    {
        $this->http = $http;
        $this->config = $config;
    }

    /**
     * @param array $params
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function send(array $params)
    {
        $endpoint = 'msgSend.php';
        $payload = $this->preparePayload($params);

        try {
            $response = $this->http->post($endpoint, $payload);

            if ($response->getStatusCode() == 200) {
                return [
                    'code' => $code = (string) $response->getBody(),
                    'message' => $this->msgSendResponse($code),
                ];
            }
            throw CouldNotSendNotification::someErrorWhenSendingSms($response);
        } catch (RequestException $exception) {
            throw CouldNotSendNotification::couldNotSendRequestToMobilyWs($exception);
        }
    }

    /**
     * Prepare payload for http request.
     *
     * @param $params
     *
     * @return array
     */
    protected function preparePayload($params)
    {
        $form = [
            'mobile' => $this->config->mobile,
            'password' => $this->config->password,
            'applicationType' => $this->config->applicationType,
            'lang' => $this->config->lang,
            'sender' => $this->config->sender,
            'msg' => $params['msg'],
            'numbers' => $params['numbers'],
            // For development to avoid charges
//            'dateSend' => \Carbon\Carbon::parse('+1 month')->format('m/d/Y'),
        ];

        return array_merge(
            ['form_params' => $form],
            $this->config->request
        );
    }

    /**
     * Parse the response body from mobily.ws.
     *
     * @param $code
     *
     * @return string
     */
    protected function msgSendResponse($code)
    {
        $arraySendMsg = [];
        $arraySendMsg[0] = 'لم يتم الاتصال بالخادم';
        $arraySendMsg[1] = 'تمت عملية الإرسال بنجاح';
        $arraySendMsg[2] = 'رصيدك 0 , الرجاء إعادة التعبئة حتى تتمكن من إرسال الرسائل';
        $arraySendMsg[3] = 'رصيدك غير كافي لإتمام عملية الإرسال';
        $arraySendMsg[4] = 'رقم الجوال (إسم المستخدم) غير صحيح';
        $arraySendMsg[5] = 'كلمة المرور الخاصة بالحساب غير صحيحة';
        $arraySendMsg[6] = 'صفحة الانترنت غير فعالة , حاول الارسال من جديد';
        $arraySendMsg[7] = 'نظام المدارس غير فعال';
        $arraySendMsg[8] = 'تكرار رمز المدرسة لنفس المستخدم';
        $arraySendMsg[9] = 'انتهاء الفترة التجريبية';
        $arraySendMsg[10] = 'عدد الارقام لا يساوي عدد الرسائل';
        $arraySendMsg[11] = 'اشتراكك لا يتيح لك ارسال رسائل لهذه المدرسة. يجب عليك تفعيل الاشتراك لهذه المدرسة';
        $arraySendMsg[12] = 'إصدار البوابة غير صحيح';
        $arraySendMsg[13] = 'الرقم المرسل به غير مفعل أو لا يوجد الرمز BS في نهاية الرسالة';
        $arraySendMsg[14] = 'غير مصرح لك بالإرسال بإستخدام هذا المرسل';
        $arraySendMsg[15] = 'الأرقام المرسل لها غير موجوده أو غير صحيحه';
        $arraySendMsg[16] = 'إسم المرسل فارغ، أو غير صحيح';
        $arraySendMsg[17] = 'نص الرسالة غير متوفر أو غير مشفر بشكل صحيح';
        $arraySendMsg[18] = 'تم ايقاف الارسال من المزود';
        $arraySendMsg[19] = 'لم يتم العثور على مفتاح نوع التطبيق';

        if (array_key_exists($code, $arraySendMsg)) {
            return $arraySendMsg[$code];
        }
        $message = "نتيجة العملية غير معرفه، الرجاء المحاول مجددا\n";
        $message .= 'الكود المرسل من الموقع: ';
        $message .= "{$code}";

        return $message;
    }
}
