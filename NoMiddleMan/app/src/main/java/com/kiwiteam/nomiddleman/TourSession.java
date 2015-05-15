package com.kiwiteam.nomiddleman;

/**
 * Created by Luis on 4/1/2015.
 */
public class TourSession {
    private String day;
    private String time;
    private int id;
    private int availability;

    public TourSession() {
        day = "";
        time = "";
        id = -1;
        availability = 0;
    }

    /**
     * Tour session constructor
     * @param day
     * @param time
     * @param id
     * @param availability
     */
    public TourSession(String day, String time, int id, int availability) {
        this.day = day;
        this.time = time;
        this.id = id;
        this.availability = availability;
    }

    /**
     * Returns the day of the session
     * @return
     */
    public String getSessionDay() {
        return day;
    }

    /**
     * Returns the time of the session
     * @return
     */
    public String getSessionTime() {
        return time;
    }

    /**
     * Returns the session key
     * @return
     */
    public int getSessionID() {
        return id;
    }

    /**
     * Returns the availability of the session
     * @return
     */
    public int getAvailability() {
        return availability;
    }

}
