<?
class Review {
    public int $reviewID;
    public int $userID;
    public int $targetUserID;
    public float $rating;
    public string $comment;

    public function __construct(
        int $reviewID,
        int $userID,
        int $targetUserID,
        float $rating,
        string $comment
    ) {
        $this->reviewID = $reviewID;
        $this->userID = $userID;
        $this->targetUserID = $targetUserID;
        $this->rating = $rating;
        $this->comment = $comment;
    }

    public function submitReview() {
       
    }

    public function getReviewsForUser() {
        
    }
}
