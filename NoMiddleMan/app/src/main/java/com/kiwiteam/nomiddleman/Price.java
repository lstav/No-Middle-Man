package com.kiwiteam.nomiddleman;

/**
 * Created by Luis on 4/12/2015.
 */
public class Price {
    /**
     * Converts prince in String to a double
     * @param price
     * @return Price
     */
    public static double getDouble(String price) {
        String substring = price.substring(1);

        substring = substring.replace(",","");

        double Price = Double.parseDouble(substring);
        return Price;
    }
}
