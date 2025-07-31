
<?
class Notification {
    public int $notificationID;
    public int $receiverID;
    public string $message;
    public DateTime $date;

    public function __construct(
        int $notificationID,
        int $receiverID,
        string $message,
        DateTime $date
    ) {
        $this->notificationID = $notificationID;
        $this->receiverID = $receiverID;
        $this->message = $message;
        $this->date = $date;
    }

    public function sendNotification() {
        
    }

    public function viewNotifications() {
       
    }
}


?>