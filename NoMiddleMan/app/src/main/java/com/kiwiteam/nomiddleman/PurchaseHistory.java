package com.kiwiteam.nomiddleman;

import java.util.ArrayList;

/**
 * Created by Luis on 4/1/2015.
 */
public class PurchaseHistory {
    private ArrayList<HistoryItem> item;

    public PurchaseHistory() {
        item = new ArrayList<>();
    }

    public void addToHistory(String date, String time, int sessionID, int quantity, Tour tour) {
        item.add(new HistoryItem(date, time, sessionID, quantity, tour));
    }

    public ArrayList<HistoryItem> getHistory() {
        return item;
    }

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

        public HistoryItem(String date, String time, int sessionID, int quantity, Tour tour) {
            this.date = date;
            this.time = time;
            this.sessionID = sessionID;
            this.quantity = quantity;
            this.tour = tour;
            this.isRated = false;
        }

        public String getDate() {
            return date;
        }

        public String getTime() {
            return time;
        }

        public Tour getTour() {
            return tour;
        }

        public int getQuantity() {
            return quantity;
        }

        public int getSessionID() {
            return sessionID;
        }

        public double getPrice() {
            return tour.getPrice()*quantity;
        }

        public boolean isRated() {
            return isRated;
        }

        public void rated() {
            isRated = true;
        }
    }
}
