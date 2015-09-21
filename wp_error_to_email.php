<?php
/*
Plugin Name: WP 404 Error Search Email To Admin
Plugin URI: https://github.com/iammdmusa
Description: When User Search on your site if they having 404 Error then in that particular Key Word /Query String will be email to Site Admin.
Author: Md Musa
Author URI: http://shuvomusa.me
Text Domain: wp_404_error_search_email_to_admin
Domain Path: /languages/
License: GPLv2
Version: 1.0

WP 404 Error Search Email To Admin is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

WP 404 Error Search Email To Admin is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with WP 404 Error Search Email To Admin. If not, see GPLv2 .

*/


// don't load directly
if ( ! defined( 'ABSPATH' ) ) {
    die( '-1' );
}

add_action( 'plugins_loaded', 'Musa_Push_QueryString_Info_Admin' );
if(!function_exists('MusaFetch_404_QueryStringToEmail_Admin')){
    class MusaFetch_404_QueryStringToEmail_Admin
    {
        var $searchingTime,
            $requestURI,
            $emailTo,
            $theme_data,
            $siteURL,
            $queryString,
            $remoteAddressIP,
            $httpUserAgent,
            $SendingMessagesInfo;
        function __construct()
        {
            $this->SetHeaders();
            $this->GrabAndSetupVars();
            $this->Musa_generatingEmailTemplate();
            $this->MusaSendEmailToAdmin();
        }

        public function SetHeaders()
        {
            $Current_theme_info = wp_get_theme();
            $TextDomain = $Current_theme_info->get( 'TextDomain' );
            echo header(__('HTTP/1.1 404 Not Found',$TextDomain));
            echo header(__('Status: 404 Not Found',$TextDomain));
        }

        function GrabAndSetupVars()
        {
            $this->siteURL  = home_url( '/' );
            $this->emailTo = get_bloginfo( 'admin_email' );

            /* Server and execution environment information:
             * */
            // Server request URI
            $this->requestURI = isset( $_SERVER['REQUEST_URI'] ) && isset( $_SERVER['HTTP_HOST'] ) ? $this->Musa_QueryString( 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ) : 'unknown';
            // Server query string
            $this->queryString = isset( $_SERVER['QUERY_STRING'] ) ? $this->Musa_QueryString( $_SERVER['QUERY_STRING'] ) : 'unknown';
            // Server IP address
            $this->remoteAddressIP = isset( $_SERVER['REMOTE_ADDR'] ) ? $this->Musa_QueryString( $_SERVER['REMOTE_ADDR'] ) : 'unknown';
            // User agent
            $this->httpUserAgent = isset( $_SERVER['HTTP_USER_AGENT'] ) ? $this->Musa_QueryString( $_SERVER['HTTP_USER_AGENT'] ) : 'unknown';
            // Log Time
            $this->searchingTime = $this->Musa_QueryString(date_i18n( get_option( 'date_format' ), strtotime( "F jS Y, h:i A") ));
        }

        public function Musa_QueryString( $string )
        {
            $string = rtrim( $string );
            $string = ltrim( $string );
            $string = htmlentities( $string, ENT_QUOTES );
            $string = str_replace( "\n", "<br />", $string );
            if ( get_magic_quotes_gpc() ) {
                $string = stripslashes( $string );
            }
            return $string;
        }

        public function Musa_generatingEmailTemplate()
        {
            $this->SendingMessagesInfo = '
                <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                    <!--[if IE]><![endif]-->
                    <!--[if lt IE 7 ]> <html lang="en" class="ie6">    <![endif]-->
                    <!--[if IE 7 ]>    <html lang="en" class="ie7">    <![endif]-->
                    <!--[if IE 8 ]>    <html lang="en" class="ie8">    <![endif]-->
                    <!--[if IE 9 ]>    <html lang="en" class="ie9">    <![endif]-->
                    <!--[if (gt IE 9)|!(IE)]><!-->
                    <html lang="en">
                    <!--<![endif]-->
                    <head>
                        <meta charset="utf-8">
                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1">
                        <title>404 on '.$this->queryString.'</title>
                    </head>
                    <body style="font-family:sans-serif; font-size:14px;">
                        <p style="margin: 20px auto;">
                            Hello Admin,<br>
                            Some Just visited on your this ,<i>'.$this->siteURL.'</i> and visitor search about this <b>'.$this->queryString.'</b> Keyword. A 404 Error means, your site have no keyword like this. Please
                            If this keyword related to your website context then keep note. Have a look on full details about this below.
                        </p>
                       <table  border="1" style="color:#333333;width:100%;border-width: 1px;border-color: #729ea5;border-collapse: collapse;">
                            <tr style="background-color:#ffffff;">
                                <th style="color:#fff;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;">Element</th>
                                <th style="color:#fff;background-color:#acc8cc;border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;text-align:left;">Information</th></tr>
                            <tr style="background-color:#ffffff;">
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">Searching Time</td>
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">'.$this->searchingTime.'</td>
                            </tr>
                            <tr style="background-color:#ffffff;">
                               <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">404 URL</td>
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">'.$this->requestURI.'</td>
                            </tr>
                            <tr style="background-color:#ffffff;">
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">Website URL</td>
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">'.$this->siteURL.'</td>
                            </tr>
                            <tr style="background-color:#ffffff;">
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">Search Keyword</td>
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">'.$this->queryString.'</td>
                            </tr>
                            <tr style="background-color:#ffffff;">
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">Visitor IP</td>
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">'.$this->remoteAddressIP.'</td>
                            </tr>
                            <tr style="background-color:#ffffff;">
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">User Agent</td>
                                <td style="border-width: 1px;padding: 8px;border-style: solid;border-color: #729ea5;">'.$this->httpUserAgent.'</td>
                            </tr>
                        </table>
                        <p style="margin: 20px auto;">
                           <button style="background-color:#ffffff; padding: 8px;color:#fff"><a href="mailto:musa01717@gmail.com">Donate!</a></button>
                        </p>
                    </body>
                </html>';

        }

        public function emailHeaderInfo()
        {
            return sprintf(
                'From: %s ' . "\r\n"
                . 'MIME-Version: 1.0' . "\r\n"
                . 'Content-Type: text/html; charset=ISO-8859-1' . "\r\n",
                'no-reply@webmaster.com'
            );
        }
        public function MusaSendEmailToAdmin()
        {
            add_filter('wp_mail_content_type',create_function('', 'return "text/html"; '));
            if($_REQUEST['s']!=''){
                wp_mail( $this->emailTo, 'Head UP Please!', $this->SendingMessagesInfo, sprintf( 'From: %s ', $this->emailHeaderInfo() ) );
            }
        }
    }
}
function Musa_Push_QueryString_Info_Admin()
{
    return  new MusaFetch_404_QueryStringToEmail_Admin();
}
