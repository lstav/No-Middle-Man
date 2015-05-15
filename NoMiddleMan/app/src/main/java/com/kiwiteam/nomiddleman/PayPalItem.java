package com.kiwiteam.nomiddleman;

/**
 * Class to save paypal items
 */
public class PayPalItem {
    private String gEmail = "";
    private Double price = 0.0;

    public PayPalItem(String gEmail, Double price) {
        this.gEmail = gEmail;
        this.price = price;
    }

    public String getgEmail() {
        return gEmail;
    }

    public void setgEmail(String gEmail) {
        this.gEmail = gEmail;
    }

    public Double getPrice() {
        return price;
    }

    public void setPrice(Double price) {
        this.price = this.price + price;
    }

    /**
     * Overrides equals method to check equality of two shopping cart items
     * @param sItem
     * @return
     */
    public boolean equals(PayPalItem sItem) {
        if(this.getgEmail().equals(sItem.getgEmail())) {
            return true;
        }
        return false;
    }


}
