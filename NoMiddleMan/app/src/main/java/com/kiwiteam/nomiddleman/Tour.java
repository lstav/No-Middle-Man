package com.kiwiteam.nomiddleman;

import android.graphics.Bitmap;

import java.util.ArrayList;

/**
 * Class to save tour values to be presented on the search activity, shopping cart, checkout, and purchase history
 */
public class Tour {
    private String name;
    private double price;
    private ArrayList<Bitmap> picture;
    private int id;
    private double extremeness;
    private double avg;

    /**
     * Constructor for Tour class
     * @param name
     * @param price
     * @param picture
     * @param id
     * @param extremeness
     */
    public Tour(String name, double price, ArrayList<Bitmap> picture, int id, double extremeness, double avg) {
        super();

        this.name = name;
        this.price = price;
        this.picture = picture;
        this.id = id;
        this.extremeness = extremeness;
        this.avg = avg;
    }

    /**
     * Gets tour name
     * @return
     */
    public String getName() {
        return name;
    }

    /**
     * Gets tour price
     * @return
     */
    public double getPrice() {
        return price;
    }

    /**
     * Gets pictures
     * @return
     */
    public ArrayList<Bitmap> getPictures() {
        return picture;
    }

    /**
     * Gets tour ID
     * @return
     */
    public int getId() {
        return id;
    }

    /**
     * Gets tour extremeness
     * @return
     */
    public double getExtremeness() {
        return extremeness;
    }

    public double getAvg() {
        return avg;
    }

}
