<?phpclass Trip {
    public int $trip_id;
    public int $traveller_id;
    public int $guide_id;
    public int $driver_id;
    public DateTime $date;
    public DateTime $time;
    public string $trip_status;

    public function __construct(
        int $trip_id,
        int $traveller_id,
        int $guide_id,
        int $driver_id,
        DateTime $date,
        DateTime $time,
        string $trip_status
    ) {
        $this->trip_id = $trip_id;
        $this->traveller_id = $traveller_id;
        $this->guide_id = $guide_id;
        $this->driver_id = $driver_id;
        $this->date = $date;
        $this->time = $time;
        $this->trip_status = $trip_status;
    }

    
    public function updateStatus(string $newStatus): void {
        $this->trip_status = $newStatus;
       
    }
}
