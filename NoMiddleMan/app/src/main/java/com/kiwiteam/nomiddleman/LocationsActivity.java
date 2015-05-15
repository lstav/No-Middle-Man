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
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Spinner;

import org.apache.http.HttpEntity;
import org.apache.http.HttpResponse;
import org.apache.http.client.HttpClient;
import org.apache.http.client.methods.HttpGet;
import org.apache.http.impl.client.DefaultHttpClient;
import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.util.ArrayList;


public class LocationsActivity extends ActionBarActivity implements AdapterView.OnItemSelectedListener {

    private ProgressDialog pDialog;

    private DatabaseConnection conn;

    private Spinner country;
    private Spinner state;
    private Spinner city;

    private ArrayAdapter<String> cAdapter;
    private ArrayAdapter<String> sAdapter;
    private ArrayAdapter<String> ciAdapter;

    private static String url_all_locations = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/getLocations.php";

    private ArrayList<TourLocation> tourLocationsA = new ArrayList<>();
    // JSON Node names
    private static final String TAG_SUCCESS = "success";
    private static final String TAG_LOCATIONS = "locations";
    private static final String TAG_KEY = "l_key";
    private static final String TAG_COUNTRY = "country";
    private static final String TAG_CITY = "city";
    private static final String TAG_STATE = "state";

    JSONArray location = null;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_locations);

        conn = (DatabaseConnection)getApplicationContext();

        handleIntent(getIntent());
        //registerClickCallback();
    }

    protected void onNewIntent(Intent intent) {
        setIntent(intent);
        handleIntent(intent);
        //registerClickCallback();
    }

    /**
     * Calls class to get categories from database
     * @param intent
     */
    private void handleIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        tourLocationsA.add(new TourLocation());

        new LoadAllLocations().execute();
    }

    public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
        int vId = parent.getId();
        switch (vId) {
            case R.id.country:
                String country = parent.getItemAtPosition(position).toString();
                ArrayList<String> states = getStates(country);

                state = (Spinner) findViewById(R.id.state);
                sAdapter = new ArrayAdapter<>(this,
                        android.R.layout.simple_spinner_item, states);
                sAdapter.notifyDataSetChanged();

                state.setOnItemSelectedListener(this);
                sAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                state.setAdapter(sAdapter);
                break;
            case R.id.state:
                String state = parent.getItemAtPosition(position).toString();
                ArrayList<String> cities = getCities(this.country.getSelectedItem().toString(), state);

                city = (Spinner) findViewById(R.id.city);
                ciAdapter = new ArrayAdapter<>(this,
                        android.R.layout.simple_spinner_item, cities);
                ciAdapter.notifyDataSetChanged();

                ciAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
                city.setAdapter(ciAdapter);
                break;
        }
    }

    @Override
    public void onNothingSelected(AdapterView<?> parent) {

    }

    private ArrayList<String> getCountries() {
        ArrayList<String> countries = new ArrayList<>();
        for (int i=0; i<tourLocationsA.size(); i++){
            if(!countries.contains(tourLocationsA.get(i).getCountry())) {
                countries.add(tourLocationsA.get(i).getCountry());
            }
        }
        return countries;
    }

    private ArrayList<String> getStates(String country) {
        ArrayList<String> states = new ArrayList<>();
        if(country.equals("Any")) {
            states.add("Any");
        } else {
            states.add("Any");
            for (int i = 0; i < tourLocationsA.size(); i++) {
                if (tourLocationsA.get(i).getCountry().equals(country) &&
                        !states.contains(tourLocationsA.get(i).getState())) {
                    states.add(tourLocationsA.get(i).getState());
                }
            }
        }
        return states;
    }

    private ArrayList<String> getCities(String country, String state) {
        ArrayList<String> cities = new ArrayList<>();
        if(state.equals("Any")) {
            cities.add("Any");
        } else {
            cities.add("Any");
            for (int i = 0; i < tourLocationsA.size(); i++) {
                if (tourLocationsA.get(i).getCountry().equals(country) &&
                        tourLocationsA.get(i).getState().equals(state)) {
                    cities.add(tourLocationsA.get(i).getCity());
                }
            }
        }
        return cities;
    }

    public void searchLoc(View view) {
        String country = this.country.getSelectedItem().toString();
        String state = this.state.getSelectedItem().toString();
        String city = this.city.getSelectedItem().toString();

        Intent intent = new Intent(this,SearchActivity.class);


        intent.putExtra("searchLocation","true");
        intent.putExtra("country", country);
        intent.putExtra("state", state);
        intent.putExtra("city", city);

        startActivity(intent);
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

    /**
     * Opens account information activity
     */
    public void account() {
        Intent intent = new Intent(this, AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    /**
     * Gets all categories from database
     */
    class LoadAllLocations extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(LocationsActivity.this);
            pDialog.setMessage(getString(R.string.loading));
            pDialog.setIndeterminate(false);
            pDialog.setCancelable(false);
            pDialog.show();
        }

        /**
         * Connects to php files
         * @param params
         * @return
         */
        @Override
        protected String doInBackground(String... params) {
            String result = "";

            /**
             * Calls php files to get all categories from database
             */
            try {
                HttpClient httpClient = new DefaultHttpClient();
                HttpGet httpGet = new HttpGet(url_all_locations);

                HttpResponse response = httpClient.execute(httpGet);

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
             * Gets categories in JSON object
             */
            try {
                JSONObject jObj = new JSONObject(result);
                location = jObj.getJSONArray(TAG_LOCATIONS);

                for (int i=0; i<location.length(); i++) {
                    JSONObject c = location.getJSONObject(i);
                    tourLocationsA.add(new TourLocation(c.getInt(TAG_KEY),c.getString(TAG_COUNTRY),
                            c.getString(TAG_STATE),c.getString(TAG_CITY)));
                }


            } catch (JSONException e) {
                e.printStackTrace();
            }
            return null;
        }

        /**
         * Shows all categories in activity
         * @param file_url
         */
        protected void onPostExecute(String file_url) {
            pDialog.dismiss();
            runOnUiThread(new Runnable() {
                @Override
                public void run() {

                    country = (Spinner) findViewById(R.id.country);

                    cAdapter = new ArrayAdapter<>(LocationsActivity.this, android.R.layout.simple_spinner_item, getCountries());
                    cAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

                    country.setOnItemSelectedListener(LocationsActivity.this);

                    country.setAdapter(cAdapter);



                    /*ArrayAdapter<String> adapter = new ArrayAdapter(LocationsActivity.this,
                            R.layout.result_list_categories, R.id.category, catArray);
                    listV.setAdapter(adapter);*/
                }
            });
        }
    }

}
