package com.kiwiteam.nomiddleman;

/**
 * Created by Luis on 4/28/2015.
 */
public class TourLocation {
    private String country;
    private String state;
    private String city;
    private int id;

    /**
     * Default constructor for tour location
     */
    public TourLocation() {
        country = "Any";
        state = "Any";
        city = "Any";
        id = -1;
    }

    /**
     * Tour locations contructor
     * @param id
     * @param country
     * @param state
     * @param city
     */
    public TourLocation(int id, String country, String state, String city) {
        this.id = id;
        this.country = country;
        this.state = state;
        this.city = city;
    }

    /**
     * Gets country
     * @return
     */
    public String getCountry() {
        return country;
    }

    /**
     * Gets state
     * @return
     */
    public String getState() {
        return state;
    }

    /**
     * Gets city
     * @return
     */
    public String getCity() {
        return city;
    }

    /**
     * Gets ID
     * @return
     */
    public int getId() {
        return id;
    }
}
