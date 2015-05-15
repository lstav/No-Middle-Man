package com.kiwiteam.nomiddleman;

import android.app.Application;
import android.content.Intent;
import android.content.SharedPreferences;

import java.util.ArrayList;

/**
 * Created by Luis on 3/13/2015.
 */
public class DatabaseConnection extends Application {


    private boolean isLogged = false;
    private String payPalEmail = new String();
    private String payPalPass = new String();
    private ArrayList<TourSession> tourSessions = new ArrayList<>();
    private ArrayList<TourClass> tourInformation = new ArrayList<>();
    private ArrayList<String> categories = new ArrayList<>();
    private ArrayList<String> tourNames = new ArrayList<>();
    private ArrayList<RatingClass> ratings = new ArrayList<>();
    private ShoppingCart shoppingCart;
    private PurchaseHistory purchaseHistory = new PurchaseHistory();
    private int t_key = -1;

    private static final String PREFS_NAME = "LoggedInFile";
    private static final String PREF_LOGGED = "isLogged";
    private static final String PREF_KEY = "key";
    public SharedPreferences pref;


    public void onCreate() {
        super.onCreate();
        // Gets preferences file to provide a remember me function for the application
        pref = getSharedPreferences(PREFS_NAME,MODE_PRIVATE);
        isLogged  = pref.getBoolean(PREF_LOGGED, false);
        t_key = pref.getInt(PREF_KEY, -1);
        if (isLogged != false && t_key != -1) {
            setLogged(true, t_key, true);
        }
    }

    public void rate(int tourID, double rating, String review) {

    }

    public ArrayList<ShoppingItem> getShoppingCart(int i) {
        return shoppingCart.getTours();
    }

    public void clearShoppingCart() {
        shoppingCart.clearShoppingCart();
    }

    public double getTotalPrice() {
        return shoppingCart.getTotalPrice();
    }

    public void removeFromShoppingCart(int i) {
        shoppingCart.removeFromShoppingCart(i);
    }

    public void putToursToShoppingCart(int tourID, int quantity, String day, String time) {
        //TourClass tour = getTourInformation(tourID);
        //shoppingCart.putTour(tour, quantity, day, time);
    }

    /**
     * Signs the user out and clears the preferences file
     */
    public void signout() {
        this.t_key = -1;
        isLogged = false;
        getSharedPreferences(PREFS_NAME, 0).edit().clear().commit();
        Intent intent = new Intent(this, MainActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
        startActivity(intent);
    }

    /**
     * Sets the user looged in or logged out
     * @param bool defines log status of the user
     * @param key defines tourist id
     * @param isChecked option to maintain session
     */
    public void setLogged(boolean bool, int key, boolean isChecked) {
        setT_key(key);
        isLogged = bool;
        if(isChecked) {
            getSharedPreferences(PREFS_NAME, MODE_PRIVATE).edit().putInt(PREF_KEY, key).putBoolean(PREF_LOGGED, bool).commit();
        }
    }

    /**
     * Checks if user is logged in
     * @return
     */
    public boolean isLogged() {
        return isLogged;
    }

    /**
     * Sets the tourist's key
     * @param key
     */
    public void setT_key(int key) {
        t_key = key;
    }

    /**
     * Returns the tourist's key
     * @return
     */
    public int getT_key() {
        return t_key;
    }
}
