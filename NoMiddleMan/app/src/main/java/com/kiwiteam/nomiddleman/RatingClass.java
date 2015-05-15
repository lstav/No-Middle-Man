package com.kiwiteam.nomiddleman;

/**
 * Created by Luis on 4/3/2015.
 */
public class RatingClass {
    private double rating;
    private String review;

    public RatingClass () {
        this.rating = 0;
        this.review = "";
    }

    public RatingClass (double rating, String review) {
        this.rating = rating;
        this.review = review;
    }

    /**
     * Returns the tour rating out of 5.0
     * @return rating
     */
    public double getRating() {
        return rating;
    }

    /**
     * Returns the tour review
     * @return review
     */
    public String getReview() {
        return review;
    }
}
