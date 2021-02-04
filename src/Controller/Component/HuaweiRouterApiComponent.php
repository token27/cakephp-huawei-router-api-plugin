<?php

/**
 * 
 * 
 * Version      1.0.0
 * Created      04/02/2021
 * Modified     04/02/2021
 *
 * @author      Token27
 * @copyright   2021
 */
declare(strict_types=1);

namespace Token27\HuaweiRouterApi\Controller\Component;

# CAKEPHP

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;

# PLUGIN
use Token27\HuaweiRouterApi\Utility\CustomHttpClient;

/**
 * HuaweiRouterApi component
 */
class HuaweiRouterApiComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];

    /**
     *
     * @var \Token27\HuaweiRouterApi\Utility\CustomHttpClient
     */
    protected $customHttpClient;

    /**
     * This is the one for the router I got.
     * @var string 
     */
    protected $routerIpAddress;

    /**
     * 
     * @var string 
     */
    protected $routerUsername;

    /**
     * 
     * @var string 
     */
    protected $routerPassword;

    /**
     *  These we need to acquire through an API call.
     * @var string 
     */
    private $sessionInfo = '';

    /**
     *  These we need to acquire through an API call.
     * @var string 
     */
    private $tokenInfo = '';

    /**
     * Constructor hook method.
     *
     * Implement this method to avoid having to overwrite
     * the constructor and call parent.
     *
     * @param array $config The configuration settings provided to this component.
     * @return void
     */
    public function initialize(array $config): void {
        $this->customHttpClient = new CustomHttpClient();
    }

    /**
     * Makes sure that we are ready for API usage.
     */
    private function prepare() {
        //Check to see if we have session / token.
        if (strlen($this->sessionInfo) == 0 || strlen($this->tokenInfo) == 0) {
            //We don't have any. Grab some.
            $xml = $this->customHttpClient->get($this->getRouterUrl('api/webserver/SesTokInfo'));

            $obj = new \SimpleXMLElement($xml);
            if (!property_exists($obj, 'SesInfo') || !property_exists($obj, 'TokInfo')) {
                throw new \RuntimeException('Malformed XML returned. Missing SesInfo or TokInfo nodes.');
            }

            //Set it for future use.
            $this->customHttpClient->setSecurity($obj->SesInfo, $obj->TokInfo);
        }
    }

    /**
     * Most API responses are just simple XML, so to avoid repetition
     * this function will GET the route and return the object.
     *
     * @return SimpleXMLElement
     * @throw \UnexpectedValueException
     */
    public function generalizedGet($route) {

        $this->prepare();

        $xml = $this->customHttpClient->get($this->getRouterUrl($route));
        $obj = new \SimpleXMLElement($xml);

        /**
         * Check for error message
         */
        if (property_exists($obj, 'code')) {
            throw new \UnexpectedValueException('The API returned error code: ' . $obj->code);
        }

        return $obj;
    }

    /**
     * Not all methods may work if you don't login.
     * Please note that the router is pretty aggressive
     * at timing your session out.
     * Call something periodically or just relogin on error.
     *
     * @return bool
     */
    public function login($username = null, $password = null) {
        if (!$username) {
            $username = $this->routerUsername;
        }
        if (!$password) {
            $username = $this->routerPassword;
        }
        $this->prepare();

        /*
         * Note how the router wants the password to be the following:
         * 1) Hashed by SHA256, then the raw output base64 encoded.
         * 2) The username is appended with the result of the above,
         * 	 AND the current token. Yes, the password changes everytime
         * 	 depending on what token we got. This really fucks with scrapers.
         * 3) The string from above (point 2) is then hashed by SHA256 again,
         *    and the raw output is once again base64 encoded.
         *
         * This is how the router login process works. So the password being sent
         * changes everytime depending on the current user session/token.
         * Not bad actually.
         */
        $loginXml = '<?xml version="1.0" encoding="UTF-8"?><request>
		<Username>' . $username . '</Username>
		<password_type>4</password_type>
		<Password>' . base64_encode(hash('sha256', $username . base64_encode(hash('sha256', $password, false)) . $this->customHttpClient->getToken(), false)) . '</Password>
		</request>
		';

        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/user/login'), $loginXml);
        $obj = new \SimpleXMLElement($xml);

        return (string) $obj == 'OK';
    }

    /**
     * Logout.
     *
     * @return bool
     */
    public function logout() {

        $this->prepare();

        $inboxXml = '<?xml version="1.0" encoding="UTF-8"?>
                        <request>
			<Logout>1</Logout>	
			</request>
		';
        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/user/logout'), $inboxXml);
        $obj = new \SimpleXMLElement($xml);
        return (string) $obj == 'OK';
    }

    /**
     * Checks whatever we are logged in.
     *
     * @return bool
     * @throw \UnexpectedValueException
     */
    public function isLoggedIn() {
        $obj = $this->generalizedGet('api/user/state-login');

        if (property_exists($obj, 'State')) {
            /*
             * Logged out seems to be -1
             * Logged in seems to be 0.
             * What the hell?
             */
            if ($obj->State == '0') {
                return true;
            }
        }

        return false;
    }

    /**
     * Sets the data switch to enable or disable the mobile connection.
     *
     * @return bool
     * @throw Exception
     */
    public function setDataSwitch($value) {
        if (is_int($value) === false) {
            throw new \Exception('Parameter can only be integer.');
        }
        if ($value !== 0 && $value !== 1) {
            throw new \Exception('Parameter can only be integer.');
        }

        $this->prepare();
        $dataSwitchXml = '<?xml version="1.0" encoding="UTF-8"?><request><dataswitch>' . $value . '</dataswitch></request>';

        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/dialup/mobile-dataswitch'), $dataSwitchXml);
        $obj = new \SimpleXMLElement($xml);

        return (string) $obj == 'OK';
    }

    /**
     * Gets dial up information.
     *
     * @return SimpleXMLElement
     */
    public function getDialup($type = '') {
        $endpoint = null;
        switch ($type) {
            case "connection":
            case "mobile-dataswitch":
            case "dialup-feature-switch":
            case "profiles":
            case "auto-apn":
                $endpoint = 'api/dialup/' . $type;
                break;
            default:
                break;
        }
        if ($endpoint) {
            return $this->generalizedGet($endpoint);
        }
        return false;
    }

    /**
     * Change dial up status.
     *
     * @return bool
     */
    public function setDialUp($status = 1) {

        $this->prepare();

        try {

            $inboxXml = '<?xml version="1.0" encoding="UTF-8"?>
                        <request>                      
			<Action>' . $status . '</Action>	         
			</request>
		';
            $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/dialup/dial'), $inboxXml);
            $obj = new \SimpleXMLElement($xml);
            return (string) $obj == 'OK';
        } catch (\Exception $e) {
            
        }

        return false;
    }

    /**
     * Reboot the router.
     *
     * @return bool
     */
    public function reboot($status = 1) {

        $this->prepare();

        $inboxXml = '<?xml version="1.0" encoding="UTF-8"?>
                        <request>
			<Control>' . $status . '</Control>	
			</request>
		';
        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/device/control'), $inboxXml);
        $obj = new \SimpleXMLElement($xml);
        return (string) $obj == 'OK';
    }

    /**
     * Gets the SMS inbox.
     * Page parameter is NOT null indexed and starts at 1.
     * I don't know if there is an upper limit on $count. Your milage may vary.
     * unreadPrefered should give you unread messages first.
     *
     * @return SimpleXMLElement
     */
    public function getInbox($page = 1, $count = 20, $unreadPreferred = false) {

        $this->prepare();

        $inboxXml = '<?xml version="1.0" encoding="UTF-8"?><request>
			<PageIndex>' . $page . '</PageIndex>
			<ReadCount>' . $count . '</ReadCount>
			<BoxType>1</BoxType>
			<SortType>0</SortType>
			<Ascending>0</Ascending>
			<UnreadPreferred>' . ($unreadPreferred ? '1' : '0') . '</UnreadPreferred>
			</request>
		';
        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/sms/sms-list'), $inboxXml);
        $obj = new \SimpleXMLElement($xml);

        return $obj;
    }

    /**
     * Sends SMS to specified receiver. I don't know if it works for foreign numbers,
     * but for local numbers you can just specifiy the number like you would normally
     * call it and it should work, here in Denmark "42952777" etc (mine).
     * Message parameter got the normal SMS restrictions you know and love.
     *
     * @return bool
     */
    public function sendSms($receiver, $message) {

        $this->prepare();

        /*
         * Note how it wants the length of the content also.
         * It ALSO wants the current date/time wtf? Oh well..
         */
        $sendSmsXml = '<?xml version="1.0" encoding="UTF-8"?><request>
			<Index>-1</Index>
			<Phones>
                            <Phone>' . $receiver . '</Phone>
			</Phones>
			<Sca/>
			<Content>' . $message . '</Content>
			<Length>' . strlen($message) . '</Length>
			<Reserved>1</Reserved>
			<Date>' . date('Y-m-d H:i:s') . '</Date>
			<SendType>0</SendType>
			</request>
		';
        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/sms/send-sms'), $sendSmsXml);
        $obj = new \SimpleXMLElement($xml);
        //Simple check if login is OK.
        return (string) $obj == 'OK';
    }

    /**
     * Deletes an SMS by ID, also called "Index".
     * The index on the Message object you get from getInbox
     * will contain an "Index" property with a value like "40000" and up.
     * Note: Will return true if the Index DOES NOT exist already.
     *
     * @return bool
     */
    public function deleteSms($index) {

        $this->prepare();

        $deleteXml = '<?xml version="1.0" encoding="UTF-8"?><request>
			<Index>' . $index . '</Index>
			</request>
		';
        $xml = $this->customHttpClient->postXml($this->getRouterUrl('api/sms/delete-sms'), $deleteXml);
        $obj = new \SimpleXMLElement($xml);
        //Simple check if login is OK.
        return (string) $obj == 'OK';
    }

    /**
     * Gets the current router status.
     *
     * @return SimpleXMLElement
     */
    public function getStatus() {
        return $this->generalizedGet('api/monitoring/status');
    }

    /**
     * Gets traffic statistics (numbers are in bytes).
     *
     * @return SimpleXMLElement
     */
    public function getTrafficStats() {
        return $this->generalizedGet('api/monitoring/traffic-statistics');
    }

    /**
     * Gets monthly statistics (numbers are in bytes)
     * This probably only works if you have setup a limit.
     *
     * @return SimpleXMLElement
     */
    public function getMonthStats() {
        return $this->generalizedGet('api/monitoring/month_statistics');
    }

    /**
     * Info about the current mobile network. (PLMN info).
     *
     * @return SimpleXMLElement
     */
    public function getNetwork() {
        return $this->generalizedGet('api/net/current-plmn');
    }

    /**
     * Gets the current craddle status.
     *
     * @return SimpleXMLElement
     */
    public function getCraddleStatus() {
        return $this->generalizedGet('api/cradle/status-info');
    }

    /**
     * Get current SMS count.
     *
     * @return SimpleXMLElement
     */
    public function getSmsCount() {
        return $this->generalizedGet('api/sms/sms-count');
    }

    /**
     * Get current WLAN Clients.
     *
     * @return SimpleXMLElement
     */
    public function getWlanClients() {
        return $this->generalizedGet('api/wlan/host-list');
    }

    /**
     * Get notifications on router.
     *
     * @return SimpleXMLElement
     */
    public function getNotifications() {
        return $this->generalizedGet('api/monitoring/check-notifications');
    }

    /**
     * Gets traffic statistics (numbers are in bytes).
     *
     * @return SimpleXMLElement
     */
    public function getSignal() {
        return $this->generalizedGet('api/device/signal');
    }

    /**
     * Get wifi settings.
     *
     * @return SimpleXMLElement
     */
    public function getWifiSettings() {
        return $this->generalizedGet('api/wlan/multi-basic-settings');
    }

    public function setLedOn($on = false) {

        $this->prepare();
        $ledXml = '<?xml version:"1.0" encoding="UTF-8"?><request><ledSwitch>' . ($on ? '1' : '0') . '</ledSwitch></request>';
        $xml = $this->customHttpClient->postXml($this->getUrl('api/led/circle-switch'), $ledXml);
        $obj = new \SimpleXMLElement($xml);
        return (string) $obj == 'OK';
    }

    /**
     * Internal helper that lets us build the complete URL
     * to a given route in the API.
     *
     * @return string
     */
    private function getRouterUrl($endpoint) {
        return 'http://' . $this->routerIpAddress . '/' . $endpoint;
    }

    /**
     * 
     * GETTERS AND SETTER
     * 
     */
    public function setRouterAddress(string $routerAddress): void {
        /**
         * Remove trailing slash if any.
         */
        $routerAddress = rtrim($routerAddress, '/');
        /**
         * If not it starts with http, we assume HTTP and add it.
         */
        if (strpos($routerAddress, 'http') !== 0 && strpos($routerAddress, 'https') !== 0) {
            $routerAddress = 'http://' . $routerAddress;
        }

        $this->routerIpAddress = $routerAddress;
    }

    public function getRouterAddress(): string {
        return $this->routerIpAddress;
    }

    private function _setSessionInfo(string $sessionInfo): void {
        $this->sessionInfo = $sessionInfo;
    }

    private function _setTokenInfo(string $tokenInfo): void {
        $this->tokenInfo = $tokenInfo;
    }

    public function getSessionInfo(): string {
        return $this->sessionInfo;
    }

    public function getTokenInfo(): string {
        return $this->tokenInfo;
    }

    public function getRouterIpAddress(): string {
        return $this->routerIpAddress;
    }

    public function getRouterUsername(): string {
        return $this->routerUsername;
    }

    public function getRouterPassword(): string {
        return $this->routerPassword;
    }

    public function setRouterIpAddress(string $routerIpAddress): void {
        $this->routerIpAddress = $routerIpAddress;
    }

    public function setRouterUsername(string $routerUsername): void {
        $this->routerUsername = $routerUsername;
    }

    public function setRouterPassword(string $routerPassword): void {
        $this->routerPassword = $routerPassword;
    }

}
