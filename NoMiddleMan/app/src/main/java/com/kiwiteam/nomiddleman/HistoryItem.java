package com.kiwiteam.nomiddleman;

/**
 * Created by Luis on 4/26/2015.
 */
public class HistoryItem {
    private String date;
    private String time;
    private int sessionID;
    private int quantity;
    private Tour tour;
    private boolean isRated;

    public HistoryItem() {
        date = new String();
        time = new String();
        sessionID = -1;
        quantity = 0;
        //tour = new TourClass();
        isRated = false;
    }

    /**
     * History Item constructor
     * @param date
     * @param time
     * @param sessionID
     * @param quantity
     * @param isRated
     * @param tour
     */
    public HistoryItem(String date, String time, int sessionID, int quantity, boolean isRated, Tour tour) {
        this.date = date;
        this.time = time;
        this.sessionID = sessionID;
        this.quantity = quantity;
        this.tour = tour;
        this.isRated = isRated;
    }

    /**
     * Gets date
     * @return
     */
    public String getDate() {
        return date;
    }

    /**
     * Gets time
     * @return
     */
    public String getTime() {
        return time;
    }

    /**
     * Gets tour
     * @return
     */
    public Tour getTour() {
        return tour;
    }

    /**
     * Gets quantity
     * @return
     */
    public int getQuantity() {
        return quantity;
    }

    /**
     * Gets session ID
     * @return
     */
    public int getSessionID() {
        return sessionID;
    }

    /**
     * Gets price
     * @return
     */
    public double getPrice() {
        return tour.getPrice();
    }

    /**
     * Gets is rated
     * @return
     */
    public boolean isRated() {
        return isRated;
    }

    /**
     * Sets rated
     * @return
     */
    public void rated() {
        isRated = true;
    }
}