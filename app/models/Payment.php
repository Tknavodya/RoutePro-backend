<?
class Payment {
    public int $paymentID;
    public int $bookingID;
    public float $amount;
    public string $method;
    public string $status;

    public function __construct(
        int $paymentID,
        int $bookingID,
        float $amount,
        string $method,
        string $status
    ) {
        $this->paymentID = $paymentID;
        $this->bookingID = $bookingID;
        $this->amount = $amount;
        $this->method = $method;
        $this->status = $status;
    }

    public function processPayment(): bool {
        
        $this->status = "completed";
        return true;
    }

    public function refund(): bool {
    
        $this->status = "refunded";
        return true;
    }
}
