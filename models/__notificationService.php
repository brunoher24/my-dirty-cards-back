<?php
  require_once dirname(__DIR__).'/vendor/autoload.php';

  class NotificationService
  {
    public $pusher;
 
    const OPTIONS = array(
      'cluster' => 'eu',
      'useTLS' => true
    );

     public function __construct() { 
      $this->pusher = new Pusher\Pusher(
        '75f76708cd7f214f633a',
        'e6ee541a18dcf1ace1cd',
        '1240016',
        self::OPTIONS
      );
    }
 
    public function send($data)
    {
      try {
        $stmt = $this->pusher->trigger($data["chanelName"], $data["eventName"], $data["content"]);
        return $stmt;
      } catch(Exception $e) {
        return ["error", $e->getMessage(), null, null, 501];
      }
      
    }
 
  }
?>

