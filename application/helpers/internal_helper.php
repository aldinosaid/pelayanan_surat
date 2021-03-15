<?php
include APPPATH . "third_party/ImageResize.php";
use \Eventviva\ImageResize;

function cnull($value)
{
    return (empty($value))?'':$value;
}

function application_version()
{
    return "Desa Jatisawit Lor - V0.0.01";
}

function user_level($user_level)
{
    switch ($user_level) {
        case 1:
            return "Kasir";
            break;
            
        default:
            return "Admin";
            break;
    }
}
    
function is_logged_in()
{
    $CI =& get_instance();
    $unique_code = $CI->session->userdata('username');
        
    if (!isset($unique_code) || empty($unique_code)) {
        return false;
    } else {
        return true;
    }
}

function voter_logged_in()
{
    $CI =& get_instance();
    $unique_code = $CI->session->userdata('fbId');
        
    if (!isset($unique_code) || empty($unique_code)) {
        return false;
    } else {
        return $unique_code;
    }
}

function campaign_active()
{
    $CI = get_instance();
    $CI->load->model('settings_model');
    $campaign = $CI->settings_model->getSettings('campaign_active');
    return $campaign->campaign_name;
}

function email_api()
{
    $CI = get_instance();
    $CI->load->model('settings_model');
    $email_api = $CI->settings_model->getSettings('email_api');
    return $email_api;
}

function get_campaign_id($campaign_name = '')
{
    $CI = get_instance();
    $CI->load->model('campaign_model');
    $campaign = $CI->campaign_model->getCampaignId($campaign_name);
    if (sizeof($campaign)) {
        return $campaign[0]->id;
    } else {
        return false;
    }
}

function getTotalReport($report_data)
{
    $cost = 0;
    foreach ($report_data as $key => $value) {
        $cost += $value->total;
    }
    return $cost;
}

function get_total_vote($unique_code = '', $campaign_id = '')
{
    $CI = get_instance();
    $CI->load->model('vote_model');
    $data = [
    'unique_code' => $unique_code,
    'campaign_id' => $campaign_id
    ];
    $totalVote = $CI->vote_model->getVottingCount($data);
    return $totalVote;
}
    
function get_session($sDataSession, $with_unset = false)
{
    $CI =& get_instance();
    $sess = isset($CI->session->userdata['ses_'.$sDataSession])?$CI->session->userdata['ses_'.$sDataSession]:false;
    if ($with_unset) {
        unset_session($sDataSession);
    }
    return $sess;
}

function send_email($to, $subject, $html_content)
{
    $email_api = email_api();
    if ($email_api->enabled) {
        send_mail_mailgun($to, $subject, $html_content);
    } else {
        php_mail($to, $subject, $html_content);
    }
}

function php_mail($to, $subject, $html_content)
{

    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=iso-8859-1';
    $headers[] = 'Reply-To: info@jafra.co.id';
    $headers[] = 'From: Face of Jafra <info@jafra.co.id>';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    mail($to, $subject, $html_content, implode("\r\n", $headers));
}

function set_session($sDataSession, $sValueSession)
{
    $CI =& get_instance();
    return $CI->session->set_userdata($sDataSession, $sValueSession);
}
function unset_session($sDataSession)
{
    $CI =& get_instance();
    return $CI->session->unset_userdata('ses_'.$sDataSession);
}
    
function convert_to_option($result, $field = 'field')
{
    $opt="";
    foreach ($result as $var) {
        $opt .= "<option id='".strtolower($var[$field])."'>".$var[$field]."</option>";
    }
    return $opt;
}
function paginate($total, $per_page = 20, $uri_key = 'page', $link_suffix = '')
{
    $CI =& get_instance();
    $per_page = intval($per_page);
    if ($per_page <= 0) {
        $per_page = 20;
    }

    $uri_segment = null;
    $uri_array = $CI->uri->segment_array();


    foreach ($uri_array as $i => $segment_name) {
        if ($uri_key == $segment_name) {
            $uri_segment = $i;
            break;
        }
    }

    $is_odd = (!empty($uri_segment) and $uri_segment % 2 == 0);

    $uri = $CI->uri->uri_to_assoc((!$is_odd ? 1 : 2));

    unset($uri[$uri_key]);



    if (count($uri) == 1 and reset($uri) === false) {
        $key = reset(array_keys($uri));
        $uri[ $key ] = '';
    }
        
    $CI->config->load('pagination');

    $config = $CI->config->item('pagination');
        

    $base_url = $CI->uri->assoc_to_uri($uri) . $uri_key;


    if ($is_odd) {
        $base_url = $CI->uri->segment(1) . '/' . $base_url;
    }

    $config['base_url'] = site_url($base_url);
    $config['per_page'] = $per_page;
    $config['total_rows'] = $total;
    $config['uri_segment'] = $uri_segment + 1;

    $CI->load->library('pagination', $config);

    $links = $CI->pagination->create_links();

    if (!empty($link_suffix)) {
        $links = preg_replace('/'.$uri_key.'\/([0-9]+)?/', '${0}'.$link_suffix, $links);
    }

    return $links;
}

function debug_pre($data = '')
{
    echo "<pre>";
    var_dump($data);
    echo "<pre>";
}

function random_string($length = 0)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    for ($i = 0; $i < $length; $i++) {
        $randstring .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randstring;
}

function is_strong_password($password = '')
{
    $strong_regex = "/^(?=^.{8,}$)(?=(.*\\d){2})(?=(.*[A-Za-z]){2})(?=(.*[!@#$%^&*?]){2})(?!.*[\\s])^.*/";
    $sequence = "/^(?=(.*(0123|1234|2345|3456|4567|5678|6789|7890)))|(?=(.*(abcd|bcde|cdef|defg|efgh|fghi|ghij|hijk|ijkl|jklm|klmn|lmno|mnop|nopq|opqr|pqrs|qrst|rstu|stuv|tuvw|uvwx|vwxy|wxyz)))/";
    if (preg_match($strong_regex, $password)) {
        if (preg_match($sequence, $password)) {
            return false;
        } else {
            return true;
        }
    } else {
        if (preg_match($sequence, $password)) {
            return false;
        } else {
            return false;
        }
    }
}

function send_mail_mailgun($to, $subject, $html_content)
{
    $email_api = email_api();
    // $key = "key-54a3fafb596d89d4ace965997c00e031";
    // $url = "https://api.mailgun.net/v3/mg.redcomm.co.id/messages";
    $key = $email_api->api_key;
    $url = $email_api->api_url;

    $config = [
    'api_key'   => $key,
    'api_url'   => $url
    ];

    $message = [
    'from'          => 'Face of Jafra <info@jafra.co.id>',
    'to'            => $to,
    'h:Reply-To'    => 'info@jafra.co.id',
    'subject'       => $subject,
    'html'          => $html_content,
    'text'          => ''
    ];

    // $message = $args;
    // echo $html_content;exit();
    $ch = curl_init();
     
    curl_setopt($ch, CURLOPT_URL, $config['api_url']);
    curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD, "api:{$config['api_key']}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
    curl_setopt($ch, CURLOPT_VERBOSE, true);

    $result = curl_exec($ch);
}

function idr_format($currency)
{
    return 'Rp ' . strrev(implode(',', str_split(strrev(strval($currency)), 3))) . '-,';
}

function dateToSql($day, $month, $years)
{
    return $years . '-' . $month . '-' . $day;
}

function stringToPartDate($date, $format)
{
    switch ($format) {
        case 'dd':
            return (int)date('d', strtotime($date));
            break;
        case 'mm':
            return (int)date('m', strtotime($date));
            break;
        case 'YYYY':
            return (int)date('Y', strtotime($date));
            break;
    }
}

function idTimeFormat($dateTime)
{
    return date('d M Y', strtotime($dateTime));
}

function idrToString($subject = null)
{
    $search = [
    'Rp ',
    ',',
    '.',
    '-,'
    ];

    $replace = [
    ''
    ];

    return str_ireplace($search, $replace, $subject);
}
function phoneToString($subject = null)
{
    $search = [
    '+62(8',
    ')',
    '-'
    ];

    $replace = [
    ''
    ];

    return str_ireplace($search, $replace, $subject);
}

function uploadOriginalImage($file = null)
{
    $image = ImageResize::createFromString($file);
    return $image;
}

function uploadThumbImage($file = null)
{
    $image = ImageResize::createFromString($file);
    $image->crop(200, 200);
    return $image;
}

function get_alias($judul = '')
{
    $alias = null;
    if ($judul) {
        $alias = str_ireplace(' ', '-', strtolower($judul));
    }
    return $alias;
}

function upload_thumbnail_proporsional($file = null){
    $image = ImageResize::createFromString($file);
    $image->scale(65);
    return $image;
}

function ambil_pengaturan($key)
{
    $CI = get_instance();
    $CI->load->model('pengaturan_model');
    $pengaturan = $CI->pengaturan_model->get_settings();
    if (!empty($pengaturan[$key])) {
        return $pengaturan[$key];
    } else {
        return '';
    }
}

function upload_banner($file = null)
{
    $image = ImageResize::createFromString($file);
    return $image->resize(1920, 1000);
}

function get_link_foto($file = null)
{
    if ($file) {
        return 'uploads/images/gallery/foto/'.$file;
    }
    return '';
}

function get_snippet($text, $start = 0, $max_length = 100)
{
    return substr($text, $start, $max_length);
}

function get_list_category()
{
    $CI =& get_instance();
    $CI->load->model('artikel_model');
    $categories = $CI->artikel_model->get_all_category();
    $html = "<ul>";
    foreach ($categories as $category) {
        $html .= '<li><a href="'.base_url('artikel/kategori/'. $category->alias).'">' . $category->nama_kategori . '</a></li>';
    }

    return $html;
}

function get_all_jabatan()
{
    return array(
        0 => array(
                'id' => 1,
                'jabatan' => 'KEPALA DESA'
            ),
        1 => array(
                'id' => 2,
                'jabatan' => 'SEKRETARIS DESA'
            ),
        2 => array(
                'id' => 3,
                'jabatan' => 'LURAH'
            ),
        3 => array(
                'id' => 4,
                'jabatan' => 'KLIWON'
            )
    );
}

function get_jabatan($id = 0)
{
    $all_jabatan = get_all_jabatan();
    foreach ( $all_jabatan as $jabatan) {
        if ($jabatan['id'] == $id) {
            return $jabatan['jabatan'];
        }
    }
}

function getFormField($field)
{
    
}