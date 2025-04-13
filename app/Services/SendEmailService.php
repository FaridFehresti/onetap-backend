<?php

namespace App\Services;

use App\Mail\CustomEmail;
use Illuminate\Support\Facades\Mail;

class SendEmailService
{
    /**
     * ارسال ایمیل به مقصد با ایمیل مبدا مشخص
     *
     * @param string $senderEmail
     * @param string $title
     * @param string $description
     * @return bool
     */
    public function sendEmail($senderEmail, $title, $description)
    {
        // ارسال ایمیل به مقصد با ایمیل مبدا که از پارامتر ورودی دریافت می‌کنیم
        Mail::to(env('MAIL_FROM_ADDRESS'))  // ایمیل مقصد از تنظیمات env گرفته می‌شود
        ->send((new CustomEmail($title, $description))->from($senderEmail)); // ایمیل مبدا از درخواست

        return true; // ایمیل با موفقیت ارسال شد
    }
}
