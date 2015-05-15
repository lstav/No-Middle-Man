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
import java.util.ArrayList;
import java.util.List;


public class AccountActivity extends ActionBarActivity {

    private DatabaseConnection conn;
    private int index;
    private Intent intent;
    private ProgressDialog pDialog;

    private String email;
    private String fname;
    private String lname;
    private String address;
    private String telephone;

    private static final String TAG_EMAIL = "t_Email";
    private static final String TAG_FNAME = "t_FName";
    private static final String TAG_LNAME = "t_LName";
    private static final String TAG_ADDRESS = "t_Address";
    private static final String TAG_TELEPHONE = "t_telephone";

    private JSONArray tourist;

    private static String url_get_tourist = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/getTourist.php";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_account);

        intent = getIntent();

        conn = (DatabaseConnection)getApplicationContext();
        fillText();

    }

    /**
     * Shows account information to the tourist
     */
    private void fillText() {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        index = conn.getT_key();
        new LoadTouristPage().execute();
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

    /**
     * Access account activity
     */
    public void account() {
        Intent intent = new Intent(this, AccountActivity.class);
        startActivity(intent);
    }

    /**
     * Opens the purchase history activity
     * @param view
     */
    public void purchaseHistory(View view) {
        Intent intent = new Intent(this, PurchaseHistoryActivity.class);
        startActivity(intent);
    }

    public void upcomingHistory(View view) {
        Intent intent = new Intent(this, UpcomingPurchaseHistoryActivity.class);
        startActivity(intent);
    }

    /**
     * Open the change password activity
     * @param view
     */
    public void changePassword(View view) {
        Intent intent = new Intent(this, ChangePasswordActivity.class);
        startActivity(intent);
    }

    /**
     * Open the report activity
     * @param view
     */
    public void report(View view) {
        Intent intent = new Intent(this, ReportActivity.class);
        startActivity(intent);
    }

    /**
     * Gets tourist information from the database
     */
    class LoadTouristPage extends AsyncTask<String, String, String> {
        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(AccountActivity.this);
            pDialog.setMessage("Loading results. Please wait...");
            pDialog.setIndeterminate(false);
            pDialog.setCancelable(true);
            pDialog.show();
        }

        @Override
        protected String doInBackground(String... params) {
            String result = "";

            /**
             * Connects to PHP files to access database
             */
            try {
                HttpClient httpClient = new DefaultHttpClient();
                String url;

                /**
                 * Parameters for php file
                 */
                List<NameValuePair> categoryName = new ArrayList<>();
                categoryName.add(new BasicNameValuePair("t_key", Integer.toString(index)));

                HttpPost httppost = new HttpPost(url_get_tourist);

                httppost.setEntity(new UrlEncodedFormEntity(categoryName));

                HttpResponse response = httpClient.execute(httppost);

                HttpEntity entity = response.getEntity();
                InputStream webs = entity.getContent();

                /**
                 * Saves JSON response from php file
                 */
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
             * Gets tourist array from JSON and saves values in java objects
             */
            try {
                JSONObject jObj = new JSONObject(result);
                tourist = jObj.getJSONArray("tourist");

                for (int i=0; i<tourist.length(); i++) {
                    JSONObject c = tourist.getJSONObject(i);

                    email = c.getString(TAG_EMAIL);
                    fname = c.getString(TAG_FNAME);
                    lname = c.getString(TAG_LNAME);
                    address = c.getString(TAG_ADDRESS);
                    telephone = c.getString(TAG_TELEPHONE);
                }
            } catch (JSONException e) {
                e.printStackTrace();
            }

            return null;
        }

        /**
         * Shows account information on activity
         */
        protected void onPostExecute(String file_url) {
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    TextView fName = (TextView) findViewById(R.id.userName);
                    fName.setText(fname);

                    TextView lName = (TextView) findViewById(R.id.userLName);
                    lName.setText(lname);

                    TextView emailT = (TextView) findViewById(R.id.email);
                    emailT.setText(email);

                    TextView addressT = (TextView) findViewById(R.id.address);
                    addressT.setText(address);

                    TextView tel = (TextView) findViewById(R.id.telephone);
                    tel.setText(telephone);

                }
            });
            pDialog.dismiss();
        }

    }
}
