package com.kiwiteam.nomiddleman;

import android.app.ProgressDialog;
import android.app.SearchManager;
import android.app.SearchableInfo;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.widget.SearchView;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
import android.widget.RatingBar;
import android.widget.Toast;

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
import java.util.ArrayList;
import java.util.List;


public class RateActivity extends ActionBarActivity {

    private DatabaseConnection conn;
    private int ts_ID;
    private ProgressDialog pDialog;
    private int index;
    private int success;
    double rating;
    String review;

    private static final String TAG_SUCCESS = "success";

    private JSONArray response;

    private static String url_rate = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/rate.php";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_rate);
        conn = (DatabaseConnection) getApplicationContext();
        Intent intent = getIntent();
        ts_ID = intent.getIntExtra("TourSession ID",-1);
        index = conn.getT_key();

        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

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

    /**
     * Submit rating and review
     * @param view
     */
    public void submit(View view) {
        RatingBar rBar = (RatingBar) findViewById(R.id.ratingBar);
        rating = (double) rBar.getRating();

        if(rating != 0) {
            EditText rev = (EditText) findViewById(R.id.reviewTour);
            review = rev.getText().toString();

            new Rate().execute();
        } else {
            Toast.makeText(getApplicationContext(), R.string.rate_warning, Toast.LENGTH_SHORT).show();
        }


    }

    /**
     * Returns to previous activity
     * @param view
     */
    public void cancel(View view) {
        Intent intent = new Intent(RateActivity.this, PurchaseHistoryActivity.class);
        startActivity(intent);
        finish();
    }

    /**
     * Sends new password to database and updates the database
     */
    class Rate extends AsyncTask<String, String, String> {
        /*protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(RateActivity.this);
            pDialog.setMessage("Loading results. Please wait...");
            pDialog.setIndeterminate(false);
            pDialog.setCancelable(true);
            pDialog.show();
        }*/

        @Override
        protected String doInBackground(String... params) {
            String result = "";

            /**
             * Sends parameters to php file to change password
             */
            try {
                HttpClient httpClient = new DefaultHttpClient();
                String url;

                List<NameValuePair> categoryName = new ArrayList<>();
                categoryName.add(new BasicNameValuePair("t_key", Integer.toString(index)));
                categoryName.add(new BasicNameValuePair("ts_key", Integer.toString(ts_ID)));
                categoryName.add(new BasicNameValuePair("review", review));
                categoryName.add(new BasicNameValuePair("rate", Double.toString(rating)));

                System.out.println("T_key " + index + " Ts_key " + ts_ID + " Review " + review + " Rating " + rating);

                HttpPost httppost = new HttpPost(url_rate);

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

            /**
             * Gets success in changing password
             */
            try {
                JSONObject jObj = new JSONObject(result);

                success = jObj.getInt(TAG_SUCCESS);

            } catch (JSONException e) {
                e.printStackTrace();
            }

            return null;
        }

        /**
         * If password change was successful, go back to previous activity, else, give feedback to user
         * @param file_url
         */
        protected void onPostExecute(String file_url) {
            /*if(pDialog.isShowing()) {
                pDialog.dismiss();
            }*/
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    if(success == 1) {
                        Intent intent = new Intent(RateActivity.this, PurchaseHistoryActivity.class);
                        startActivity(intent);
                        finish();
                    } else {
                        Toast.makeText(getApplicationContext(), R.string.not_rate, Toast.LENGTH_SHORT).show();
                    }
                }
            });
        }

    }

}
