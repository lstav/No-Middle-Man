package com.kiwiteam.nomiddleman;

import android.app.ProgressDialog;
import android.app.SearchManager;
import android.app.SearchableInfo;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.widget.SearchView;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListAdapter;
import android.widget.ListView;
import android.widget.TextView;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.NameValuePair;
import org.apache.http.client.HttpClient;
import org.apache.http.client.entity.UrlEncodedFormEntity;
import org.apache.http.client.methods.HttpPost;
import org.apache.http.impl.client.DefaultHttpClient;
import org.apache.http.message.BasicNameValuePair;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


public class UpcomingPurchaseHistoryActivity extends ActionBarActivity {

    private DatabaseConnection conn;

    private ArrayAdapter<HistoryItem> activeAdapter;
    private ArrayAdapter<HistoryItem> pastAdapter;
    private ListView upcomingListView;
    private ListView pastListView;
    private List<HistoryItem> activeHistory = new ArrayList<>();
    private List<HistoryItem> pastHistory = new ArrayList<>();

    private double totalPrice = 0.0;
    private int ts_key = -1;
    private int success = 0;
    private boolean active = true;

    private Bitmap bitmap;
    private ProgressDialog pDialog;
    private ImageView picture;

    private String message = new String();

    private JSONArray backup;

    private static final String TAG_KEY = "key";
    private static final String TAG_TSKEY = "ts_key";
    private static final String TAG_NAME = "name";
    private static final String TAG_PRICE = "total";
    private static final String TAG_EXTREMENESS = "extremeness";
    private static final String TAG_PHOTO = "photo";
    private static final String TAG_QUANTITY = "quantity";
    private static final String TAG_DATE = "date";
    private static final String TAG_TIME = "time";
    private static final String TAG_ACTIVE = "isActive";
    private static final String TAG_SUCCESS = "success";
    private static final String TAG_RATED = "isRated";
    private static final String TAG_AVG = "avg";


    private static String url_get_upcoming_tours = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/getUpcomingTours.php";
    //private static String url_get_past_tours = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/getPastTours.php";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_upcoming_purchase_history);

        conn = (DatabaseConnection) getApplicationContext();
        Intent intent = getIntent();
        handleIntent(intent);
    }

    /*public void onResume() {
        super.onResume();
        Intent intent = getIntent();
        handleIntent(intent);
    }*/

    public static void setListViewHeightBasedOnChildren(ListView listView) {
        ListAdapter listAdapter = listView.getAdapter();
        if (listAdapter == null)
            return;

        int desiredWidth = View.MeasureSpec.makeMeasureSpec(listView.getWidth(), View.MeasureSpec.UNSPECIFIED);
        int totalHeight = 0;
        View view = null;
        for (int i = 0; i < listAdapter.getCount(); i++) {
            view = listAdapter.getView(i, view, listView);
            if (i == 0)
                view.setLayoutParams(new ViewGroup.LayoutParams(desiredWidth, ViewGroup.LayoutParams.WRAP_CONTENT));

            view.measure(desiredWidth, View.MeasureSpec.UNSPECIFIED);
            totalHeight += view.getMeasuredHeight();
        }

        ViewGroup.LayoutParams params = listView.getLayoutParams();
        params.height = totalHeight + (listView.getDividerHeight() * (listAdapter.getCount() - 1));
        listView.setLayoutParams(params);
        listView.requestLayout();
    }

    private void handleIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        new LoadUpcomingTours().execute();
        //new LoadPastTours().execute();

        /*if(pDialog.isShowing()) {
            pDialog.dismiss();
        }*/

       /* purchaseHistory = conn.getHistory();
        ArrayList<PurchaseHistory.HistoryItem> activeHistory = new ArrayList<>();
        ArrayList<PurchaseHistory.HistoryItem> pastHistory = new ArrayList<>();

        for(int i=0; i<purchaseHistory.size(); i++) {
            if(purchaseHistory.get(i).getTour().sessionIsActive(purchaseHistory.get(i).getSessionID())) {
                activeHistory.add(purchaseHistory.get(i));
                findViewById(R.id.noUpcomingTours).setVisibility(View.GONE);
                System.out.println("Active Index " + i);
            } else {
                findViewById(R.id.pastTours).setVisibility(View.GONE);
                pastHistory.add(purchaseHistory.get(i));
            }
        }

        activeAdapter = new MyListAdapter(activeHistory);

        pastAdapter = new MyListAdapter(pastHistory);

        upcomingListView = (ListView) findViewById(R.id.upcommingListView);
        pastListView = (ListView) findViewById(R.id.pastListView);

        upcomingListView.setAdapter(activeAdapter);
        pastListView.setAdapter(pastAdapter);

        setListViewHeightBasedOnChildren(upcomingListView);
        setListViewHeightBasedOnChildren(pastListView);*/
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.global, menu);

        if (conn.isLogged())
        {
            menu.findItem(R.id.account).setVisible(true);
            menu.findItem(R.id.signout).setVisible(true);
        } else {
            menu.findItem(R.id.account).setVisible(false);
            menu.findItem(R.id.signout).setVisible(false);
        }
        //initSearchView(menu);
        SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        SearchView searchView = (SearchView) menu.findItem(R.id.action_search).getActionView();
        SearchableInfo searchableInfo = searchManager.getSearchableInfo(getComponentName());
        searchView.setSearchableInfo(searchableInfo);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        switch (item.getItemId()) {
            case R.id.action_search:
                return true;
            case android.R.id.home:
                NavUtils.navigateUpFromSameTask(this);
                return true;
            case R.id.home:
                Intent intent = new Intent(this, MainActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                return true;
            case R.id.action_cart:
                intent = new Intent(this, ShoppingCartActivity.class);
                startActivity(intent);
                return true;
            case R.id.account:
                account();
                return true;
            case R.id.signout:
                conn.signout();
                recreate();
                return true;
        }

        return super.onOptionsItemSelected(item);
    }

    public void account() {
        Intent intent = new Intent(this, AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    private class MyListAdapter extends ArrayAdapter<HistoryItem> {
        //private List<HistoryItem> pHistory;

        //public MyListAdapter(ArrayList<HistoryItem> purchaseHistory) {
        public MyListAdapter() {
            super(UpcomingPurchaseHistoryActivity.this, R.layout.shopping_cart_item, activeHistory);
            //pHistory = purchaseHistory;
        }

        public View getView(final int position, View convertView, ViewGroup parent) {
            View itemView = convertView;

            if (itemView == null) {
                itemView = getLayoutInflater().inflate(R.layout.shopping_cart_item, parent, false);

            }

            itemView.findViewById(R.id.remove).setVisibility(View.GONE);

            // find the list
            HistoryItem currentTour = activeHistory.get(position);

            picture = (ImageView) itemView.findViewById(R.id.tourPic);
            picture.setImageBitmap(currentTour.getTour().getPictures().get(0));

            TextView tName = (TextView) itemView.findViewById(R.id.tourName);
            tName.setText(currentTour.getTour().getName());

            TextView tPrice = (TextView) itemView.findViewById(R.id.tourPrice);
            double price = currentTour.getPrice();
            tPrice.setText("$"+ String.format("%.2f", price));

            TextView tQuantity = (TextView) itemView.findViewById(R.id.quantity);
            tQuantity.setText(Integer.toString(currentTour.getQuantity()));

            TextView tDate = (TextView) itemView.findViewById(R.id.date);
            tDate.setText(currentTour.getDate());

            TextView tTime = (TextView) itemView.findViewById(R.id.time);
            tTime.setText(currentTour.getTime());


            itemView.findViewById(R.id.rate).setVisibility(View.GONE);


            itemView.findViewById(R.id.tourPic).setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    Intent intent = new Intent(getApplicationContext(), TourPageActivity.class);
                    intent.putExtra("tourId",activeHistory.get(position).getTour().getId());
                    startActivity(intent);
                }
            });

            return itemView;
        }
    }

    /**
     * Search database with results by keyword
     */
    class LoadUpcomingTours extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(UpcomingPurchaseHistoryActivity.this);
            pDialog.setMessage("Loading results. Please wait...");
            pDialog.setIndeterminate(false);
            pDialog.setCancelable(true);
            pDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String result = "";

            try {
                HttpClient httpClient = new DefaultHttpClient();
                String url;

                List<NameValuePair> categoryName = new ArrayList<>();
                categoryName.add(new BasicNameValuePair("t_key", Integer.toString(conn.getT_key())));

                HttpPost httppost = new HttpPost(url_get_upcoming_tours);

                httppost.setEntity(new UrlEncodedFormEntity(categoryName));

                HttpResponse response = httpClient.execute(httppost);

                HttpEntity entity = response.getEntity();
                InputStream webs = entity.getContent();

                try {
                    BufferedReader reader = new BufferedReader(new InputStreamReader(webs,"iso-8859-1"),8);
                    StringBuilder sb = new StringBuilder();
                    String line = null;
                    while ((line = reader.readLine()) != null) {
                        sb.append(line);
                    }
                    webs.close();
                    result=sb.toString();
                } catch (Exception e) {
                    e.printStackTrace();
                }
            } catch (Exception e) {
                e.printStackTrace();
            }

            try {
                JSONObject jObj = new JSONObject(result);

                success = jObj.getInt(TAG_SUCCESS);

                if(success == 1) {

                    activeHistory.clear();
                    backup = jObj.getJSONArray("tours");

                    for (int i=0; i<backup.length(); i++) {
                        JSONObject c = backup.getJSONObject(i);
                        try {
                            BitmapFactory.Options options = new BitmapFactory.Options();
                            options.inJustDecodeBounds = true;
                            // Calculate inSampleSize
                            options.inSampleSize = 5;
                            // Decode bitmap with inSampleSize set
                            options.inJustDecodeBounds = false;

                            bitmap = BitmapFactory.decodeStream((InputStream) new URL(c.getString(TAG_PHOTO).trim() + "1.jpg").getContent(), null, options);
                        } catch (Exception e) {
                            e.printStackTrace();
                        }

                        boolean isActive = false;
                        if(c.getString(TAG_ACTIVE).equals("t")) {
                            isActive = true;
                        }

                        boolean isRated = false;
                        if(c.getString(TAG_RATED).equals("t")) {
                            isRated = true;
                        }
                        activeHistory.add(new HistoryItem(c.getString(TAG_DATE),
                                c.getString(TAG_TIME),c.getInt(TAG_TSKEY),c.getInt(TAG_QUANTITY),
                                isRated, new Tour(c.getString(TAG_NAME),
                                Price.getDouble(c.getString(TAG_PRICE)),
                                new ArrayList<>(Arrays.asList(bitmap)),c.getInt(TAG_KEY),
                                c.getDouble(TAG_EXTREMENESS),0.0)));

                        if(isActive) {
                            totalPrice = totalPrice + Price.getDouble(c.getString(TAG_PRICE));
                        }
                    }
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }

            return null;
        }

        protected void onPostExecute(String file_url) {
            pDialog.dismiss();
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    if(success == 1) {
                        findViewById(R.id.noUpcomingTours).setVisibility(View.GONE);

                        //activeAdapter = new MyListAdapter(activeHistory);
                        activeAdapter = new MyListAdapter();

                        upcomingListView = (ListView) findViewById(R.id.upcommingListView);
                        upcomingListView.setAdapter(activeAdapter);

                        setListViewHeightBasedOnChildren(upcomingListView);

                        activeAdapter.notifyDataSetChanged();
                    }
                }
            });
        }

    }
}
