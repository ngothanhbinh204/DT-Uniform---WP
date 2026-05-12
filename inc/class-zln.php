<?php

class ZaloZNS
{
    // Ghi chú: Cấu hình Zalo ZNS API
    // Ghi chú: Cấu hình giới hạn gửi OTP
    private $otp_limit = 5; // Số lần gửi OTP tối đa trong 1 giờ
    private $otp_time_window = 3600; // Thời gian giới hạn (1 giờ = 3600 giây)

    // Ghi chú: Hàm chuẩn hóa số điện thoại (bỏ +84, thêm 0 nếu cần)
    private $zalo_api_url = "https://business.openapi.zalo.me/message/template";


    function normalizePhoneNumber($phone)
    {

        $phone = preg_replace("/[^0-9]/", "", $phone);


        if (substr($phone, 0, 1) === "0") {
            $phone = "84" . substr($phone, 1);
        } elseif (substr($phone, 0, 2) !== "84") {
            $phone = "84" . $phone;
        }

        return $phone;
    }

    // Ghi chú: Hàm kiểm tra giới hạn số lần gửi OTP
    function checkOtpLimit($phone, $limit, $time_window)
    {
        global $wpdb;
        $time_threshold = date('Y-m-d H:i:s', time() - $time_window);

        // Đếm số lần gửi OTP trong khoảng thời gian
        $query = $wpdb->prepare("SELECT COUNT(*) as count FROM {$wpdb->prefix}otp_requests WHERE phone = %s AND request_time >= %s", $phone, $time_threshold);
        $result = (int) $wpdb->get_var($query);
        if ($result === false) {
            error_log("Zalo OTP Limit Check Error 1: " . $wpdb->last_error);
            error_log("Zalo OTP Limit Check Error 2: " . $result);
            return ["allowed" => false, "error" => "Lỗi kiểm tra giới hạn OTP: " . $wpdb->last_error, "result" => $result];
        }

        if ($result >= $limit) {
            error_log("Zalo OTP Limit Check Error 3: " . $wpdb->last_error);
            error_log("Zalo OTP Limit Check Error 4: " . $result);
            return ["allowed" => false, "error" => "Bạn đã gửi mật khẩu quá số lần cho phép. Tin nhắn mật khẩu đã được gửi trong Zalo, nếu không có vui lòng thử lại sau 30 phút."];
        }

        // Lưu request OTP mới
        $insert = $wpdb->prepare(
            "INSERT INTO {$wpdb->prefix}otp_requests (phone, request_time) VALUES (%s, NOW())",
            $phone
        );

        $result = $wpdb->query($insert);

        if ($result === false) {
            error_log("Zalo OTP Limit Check Error 5: " . $wpdb->last_error);
            error_log("Zalo OTP Limit Check Error 6: " . $result);
            return ["allowed" => false, "error" => "Lỗi lưu lịch sử OTP: " . $wpdb->last_error, "result" => $result];
        }

        return ["allowed" => true];
    }

    // Ghi chú: Hàm kiểm tra số điện thoại có đăng ký Zalo
    function isZaloRegistered($phone, $access_token)
    {
        $url = "https://openapi.zalo.me/v2.0/oa/getuserinfobymsidsn";
        $data = json_encode(["msisdn" => $phone]);
        $headers = [
            "Content-Type: application/json",
            "access_token: $access_token"
        ];


        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($http_code !== 200 || !isset($result["data"]["is_zalo_user"]) || !$result["data"]["is_zalo_user"]) {
            error_log("Zalo Register Check Error: HTTP $http_code, Response: " . ($result["message"] ?? "Unknown"));
            return false;
        }
        return true;
    }

    // Ghi chú: Hàm kiểm tra số điện thoại có quan tâm OA
    function isFollowingOA($phone, $oa_id, $access_token)
    {
        $url = "https://openapi.zalo.me/v2.0/oa/getfollower";
        $data = json_encode(["msisdn" => $phone, "oa_id" => $oa_id]);
        $headers = [
            "Content-Type: application/json",
            "access_token: $access_token"
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($http_code !== 200 || !isset($result["data"]["is_follower"]) || !$result["data"]["is_follower"]) {
            error_log("Zalo Follow Check Error: HTTP $http_code, Response: " . ($result["message"] ?? "Unknown"));
            return false;
        }
        return true;
    }

    // Ghi chú: Hàm tạo mật khẩu 5 số ngẫu nhiên
    function generateRandomPassword()
    {
        return str_pad(random_int(0, 99999), 5, "0", STR_PAD_LEFT);
    }

    // Ghi chú: Hàm lưu hoặc lấy mật khẩu từ DB
    function getOrCreatePassword($phone)
    {
        global $wpdb;
        $query = $wpdb->prepare("SELECT password FROM {$wpdb->prefix}user_passwords WHERE phone = %s", $phone);
        $password = $wpdb->get_var($query);
        if ($password) {
            return $password;
        }

        // Nếu chưa có, tạo mới
        $password = $this->generateRandomPassword();

        $insert = $wpdb->prepare(
            "INSERT INTO {$wpdb->prefix}user_passwords (phone, password, created_at) VALUES (%s, %s, NOW())",
            $phone,
            $password
        );

        $result = $wpdb->query($insert);

        if ($result === false) {
            http_response_code(500);
            die(json_encode(["error" => "Lỗi lưu mật khẩu vào DB: " . $wpdb->last_error]));
        }

        return $password;
    }

    // Ghi chú: Hàm gửi tin nhắn ZNS OTP
    function sendZNSOTP($phone, $password, $access_token, $template_id, $oa_id)
    {
        $brand_name = !empty(get_option('zln_brand_name')) ? get_option('zln_brand_name') : "";
        $template_data = [
            "otp" => $password,
            "brand_name" => $brand_name,
            "password" => $password
        ];

        $payload = [
            "phone" => $phone,
            "template_id" => $template_id,
            "template_data" => $template_data,
            "tracking_id" => "otp_" . time() . "_" . uniqid()
        ];

        $headers = [
            "Content-Type: application/json",
            "access_token: $access_token"
        ];

        $ch = curl_init($this->zalo_api_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $result = json_decode($response, true);
        if ($http_code === 200 && isset($result["error"]) && $result["error"] === 0) {
            return ["success" => true, "msg_id" => $result["data"]["msg_id"]];
        } else {
            error_log("ZNS Error: HTTP $http_code, Message: " . ($result["message"] ?? "Unknown"));
            return ["success" => false, "error_code" => $result["error"] ?? null, "error" => $result["message"] ?? "Lỗi gửi ZNS"];
        }
    }
}
