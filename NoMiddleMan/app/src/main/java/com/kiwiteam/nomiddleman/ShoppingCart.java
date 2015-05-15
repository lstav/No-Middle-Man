package com.kiwiteam.nomiddleman;

import java.util.ArrayList;

/**
 * Created by Luis on 3/26/2015.
 */
public class ShoppingCart {

    private ArrayList<ShoppingItem> item = new ArrayList<>();
    private int accountID = -1;
    private double totalPrice = 0;

    public ShoppingCart(int accountID) {
        this.accountID = accountID;
    }

    /**
     * Puts tour in shopping cart
     * @param tour
     * @param sessionID
     * @param quantity
     * @param date
     * @param time
     */
    public void putTour(Tour tour, int sessionID, int quantity, String date, String time, String gEmail) {
        ShoppingItem sItem = new ShoppingItem(tour, sessionID, quantity, date, time, true, gEmail);
        boolean same = false;
        int index = -1;
        for (int i = 0; i<item.size(); i++) {
            if(item.get(i).equals(sItem)) {
                same = true;
                index = i;
                break;
            }
        }
        if(same) {
            this.item.get(index).addQuantity(quantity);
        } else {
            index = item.size();
            this.item.add(sItem);
        }
    }

    /**
     * Gets tours from shopping cart
     * @return
     */
    public ArrayList<ShoppingItem> getTours() {
        return item;
    }

    /**
     * Removes from shopping cart
     * @param position
     */
    public void removeFromShoppingCart(int position) {
        item.remove(position);
    }

    /**
     * Gets the tourist account
     * @return
     */
    public int getAccountID() {
        return accountID;
    }

    /**
     * Gets total price of shopping cart
     * @return
     */
    public double getTotalPrice() {
        double totPrice = 0.00;
        for (int i=0; i<item.size(); i++) {
            totPrice = totPrice + item.get(i).getTourPrice();
        }
        totalPrice = totPrice;
        return totalPrice;
    }

    /**
     * Clears the shopping cart
     */
    public void clearShoppingCart() {
        item.clear();
    }
}
