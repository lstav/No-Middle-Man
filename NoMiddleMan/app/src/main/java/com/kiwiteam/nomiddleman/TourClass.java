package com.kiwiteam.nomiddleman;

import android.graphics.Bitmap;

import java.util.ArrayList;

/**
 * Created by luis.tavarez on 3/25/2015.
 *
 * This class is used to store information of the tour to show on the tour information page
 */
public class TourClass {

    private int tourID;
    private String tourName;
    private String tourDescription;
    private String facebook;
    private String youtube;
    private String instagram;
    private String twitter;
    private double tourPrice;
    private double extremeness;
    private ArrayList<Bitmap> tourPictures;
    private String tourAddress;
    private String guideEmail;
    private String guideName;
    private String guideLicense;
    private String company;
    private String telephone;
    private double averageRating;
    private int rateCount;

    private ArrayList<RatingClass> tourRatings;
    private ArrayList<Double> ratings;
    private ArrayList<String> reviews;
    private ArrayList<TourSession> tourSessions;
    private ArrayList<String> tourSessionsDate;
    private ArrayList<String> tourSessionsTime;
    private ArrayList<Integer> allTourSessionAvailability;

    /**
     * Constructor for TourClass class
     * @param tourID
     * @param tourName
     * @param tourDescription
     * @param facebook
     * @param youtube
     * @param instagram
     * @param twitter
     * @param tourPrice
     * @param extremeness
     * @param tourPictures
     * @param tourAddress
     * @param guideEmail
     * @param guideName
     * @param guideLicense
     * @param company
     * @param telephone
     * @param averageRating
     * @param rateCount
     * @param tourRatings
     * @param tourSessions
     */
    public TourClass(int tourID, String tourName, String tourDescription, String facebook, String youtube, String instagram, String twitter, double tourPrice, double extremeness, ArrayList<Bitmap> tourPictures, String tourAddress, String guideEmail, String guideName, String guideLicense, String company, String telephone, double averageRating, int rateCount, ArrayList<RatingClass> tourRatings, ArrayList<TourSession> tourSessions) {
        this.tourID = tourID;
        this.tourName = tourName;
        this.tourDescription = tourDescription;
        this.facebook = facebook;
        this.youtube = youtube;
        this.instagram = instagram;
        this.twitter = twitter;
        this.tourPrice = tourPrice;
        this.extremeness = extremeness;
        this.tourPictures = tourPictures;
        this.tourAddress = tourAddress;
        this.guideEmail = guideEmail;
        this.guideName = guideName;
        this.guideLicense = guideLicense;
        this.company = company;
        this.telephone = telephone;
        this.averageRating = averageRating;
        this.rateCount = rateCount;
        this.tourRatings = tourRatings;
        this.ratings = new ArrayList<>();
        this.reviews = new ArrayList<>();
        this.tourSessions = tourSessions;
        this.tourSessionsDate = new ArrayList<>();
        this.tourSessionsTime = new ArrayList<>();
        this.allTourSessionAvailability = new ArrayList<>();
    }

    /**
     * Gets tour ID
     * @return
     */
    public int getTourID() {
        return tourID;
    }

    /**
     * Gets tour name
     * @return
     */
    public String getTourName() {
        return tourName;
    }

    /**
     * Gets tour description
     * @return
     */
    public String getTourDescription() {
        return tourDescription;
    }

    /**
     * Gets tour facebook link
     * @return
     */
    public String getFacebook() {
        return facebook;
    }

    /**
     * Gets tour video link
     * @return
     */
    public String getYoutube() {
        return youtube;
    }

    /**
     * Gets tour instagram link
     * @return
     */
    public String getInstagram() {
        return instagram;
    }

    /**
     * Gets tour twitter link
     * @return
     */
    public String getTwitter() {
        return twitter;
    }

    /**
     * Gets tour price
     * @return
     */
    public double getTourPrice() {
        return tourPrice;
    }

    /**
     * Gets tour extremeness
     * @return
     */
    public double getExtremeness() {
        return extremeness;
    }

    /**
     * Gets tour pictures
     * @return
     */
    public ArrayList<Bitmap> getTourPictures() {
        return tourPictures;
    }

    /**
     * Gets tour address
     * @return
     */
    public String getTourAddress() {
        return tourAddress;
    }

    /**
     * Gets guide email
     * @return
     */
    public String getGuideEmail() {
        return guideEmail;
    }

    /**
     * Gets guide name
     * @return
     */
    public String getGuideName() {
        return guideName;
    }

    /**
     * Gets guide license
     * @return
     */
    public String getGuideLicense() {
        return guideLicense;
    }

    /**
     * Gets guide company
     * @return
     */
    public String getCompany() {
        return company;
    }

    /**
     * Gets guide telephone
     * @return
     */
    public String getTelephone() {
        return telephone;
    }

    /**
     * Gets the average tour rating
     * @return
     */
    public double getAverageRating() {

        return averageRating;
    }

    /**
     * Gets the number of rewiews
     * @return
     */
    public int getRateCount() {
        return rateCount;
    }

    /**
     * Gets the tour sessions
     * @return
     */
    public ArrayList<TourSession> getTourSessions() {
        return tourSessions;
    }

    /**
     * Rates the tour
     * @param rating
     * @param review
     */
    public void rate(double rating, String review) {
        tourRatings.add(new RatingClass(rating, review));
    }

    /**
     * Gets all tour ratings
     * @return
     */
    public ArrayList<RatingClass> getAllRatings() {
        return tourRatings;
    }

    /**
     * Gets all tour ratings
     * @return
     */
    public ArrayList<Double> getTourRatings() {
        ratings.clear();
        for(int i = 0; i<tourRatings.size(); i++) {
            ratings.add(tourRatings.get(i).getRating());
        }
        return ratings;
    }

    /**
     * Get all tour reviews
     * @return
     */
    public ArrayList<String> getTourReviews() {
        reviews.clear();
        for(int i = 0; i<tourRatings.size(); i++) {
            reviews.add(tourRatings.get(i).getReview());
        }
        return reviews;
    }

    /**
     * Gets tour sessions date
     * @return
     */
    public ArrayList<String> getTourSessionsDate() {
        for (int i=0; i<tourSessions.size(); i++) {
            if (!tourSessionsDate.contains(tourSessions.get(i).getSessionDay())) {
                tourSessionsDate.add(tourSessions.get(i).getSessionDay());
            }
        }
        return tourSessionsDate;
    }

    /**
     * Gets tour sessions ID
     * @param date
     * @param time
     * @return
     */
    public int getTourSessionID(String date, String time) {
        for (int i=0; i<tourSessions.size(); i++) {
            if(tourSessions.get(i).getSessionDay().equals(date) && tourSessions.get(i).getSessionTime().equals(time)) {
                return tourSessions.get(i).getSessionID();
            }
        }
        return -1;
    }

    /**
     * Gets tour session time
     * @param date
     * @return
     */
    public ArrayList<String> getTourSessionsTime(String date) {
        tourSessionsTime.clear();
        for (int i=0; i<tourSessions.size(); i++) {
            if(tourSessions.get(i).getSessionDay().equals(date) &&
                    !tourSessionsTime.contains(tourSessions.get(i).getSessionTime())) {
                    tourSessionsTime.add(tourSessions.get(i).getSessionTime());
            }
        }
        return tourSessionsTime;
    }

    /**
     * Gets tour session availability
     * @param date
     * @return
     */
    public ArrayList<Integer> getAllTourSessionAvailability(String date) {
        allTourSessionAvailability.clear();
        int availabilityIndex = 0;
        for (int i=0; i<tourSessions.size(); i++) {
            if(tourSessions.get(i).getSessionDay().equals(date)) {
                availabilityIndex = availabilityIndex + tourSessions.get(i).getAvailability();
                System.out.println("Availability " + availabilityIndex);
            }
        }

        for(int i=0; i<availabilityIndex; i++){
            allTourSessionAvailability.add(i+1);
            System.out.println("I " + allTourSessionAvailability.get(i));
        }
        return allTourSessionAvailability;
    }

    /**
     * Gets tour sessions availability
     * @param date
     * @param time
     * @return
     */
    public ArrayList<Integer> getTourSessionAvailability(String date, String time) {
        ArrayList<Integer> tourAvailability = new ArrayList<>();
        int availabilityIndex = 0;
        for (int i=0; i<tourSessions.size(); i++) {
            if(tourSessions.get(i).getSessionDay().equals(date) && tourSessions.get(i).getSessionTime().equals(time)) {
                availabilityIndex = i;
                break;
            }
        }

        for(int i=0; i<tourSessions.get(availabilityIndex).getAvailability(); i++){
            tourAvailability.add(i+1);
        }

        return tourAvailability;
    }

}
