<?php

namespace Database\Factories;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition()
    {
        $faker = \Faker\Factory::create('fa_IR');
        
        return [
            'user_id' => \App\Models\User::factory(),
            'message' => $faker->randomElement([
                'درخواست شما برای "کوهنوردی در دماوند" تایید شد',
                'کاربر جدیدی به فعالیت شما پیوست',
                'یادآوری: فعالیت فردا شروع می‌شود',
                'تغییرات جدیدی در برنامه فعالیت ایجاد شده است',
                'فعالیت شما به پایان رسید',
                'نظر جدیدی برای فعالیت شما ثبت شد',
                'تعداد شرکت‌کنندگان به حداکثر رسید'
            ]),
            'is_read' => $this->faker->boolean(30), // 30% chance of being read
        ];
    }
}