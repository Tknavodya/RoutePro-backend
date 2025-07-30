<?phpclass Route {
    public int $routeID;
    public string $startLocation;
    public string $endLocation;
    public float $distance;
    public float $duration;

    public function __construct(
        int $routeID,
        string $startLocation,
        string $endLocation,
        float $distance,
        float $duration
    ) {
        $this->routeID = $routeID;
        $this->startLocation = $startLocation;
        $this->endLocation = $endLocation;
        $this->distance = $distance;
        $this->duration = $duration;
    }

    public function generateRoute(): void {
      
    }

    public function addAttractionsInOrder(): void {
      
    }

    public function calculateCost(): float {
        
        return $this->distance * $costPerKm;
    }
}
