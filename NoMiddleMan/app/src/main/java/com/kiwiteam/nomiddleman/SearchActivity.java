package com.kiwiteam.nomiddleman;

import android.app.ProgressDialog;
import android.app.SearchManager;
import android.app.SearchableInfo;
import android.content.Context;
import android.content.Intent;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.Drawable;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v4.app.NavUtils;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RatingBar;
import android.widget.SearchView;
import android.widget.Spinner;
import android.widget.TextView;
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
import java.io.IOException;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;


public class SearchActivity extends ActionBarActivity implements AdapterView.OnItemSelectedListener {

    private DatabaseConnection conn;
    private boolean selectedCategory = false;
    private boolean selectedLocation = false;
    private List<Tour> tourInfo = new ArrayList<>();
    private List<String> categories = new ArrayList<>();
    private Menu menu;
    private Spinner sortBy;
    private ArrayAdapter<CharSequence> sAdapter;
    private ListView listView;
    private ProgressDialog pDialog;
    private String query;
    private String countryQuery;
    private String stateQuery;
    private String cityQuery;

    private Spinner cSpinner;
    private ArrayAdapter<String> cAdapter;

    private String categorySelectedSpinner;

    private int success = 0;

    private Intent intent;

    private String order;
    private String by;

    private ImageView picture;
    private Bitmap bitmap;

    private JSONArray backup;
    private JSONArray backupCat;

    private static final String TAG_KEY = "key";
    private static final String TAG_NAME = "name";
    private static final String TAG_PRICE = "price";
    private static final String TAG_EXTREMENESS = "extremeness";
    private static final String TAG_PHOTO = "photo";
    private static final String TAG_CAT_NAME = "category_name";
    private static final String TAG_ORDER = "order";
    private static final String TAG_BY = "by";
    private static final String TAG_MESSAGE = "message";
    private static final String TAG_AVG = "avg";


    private static String url_search_categories = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/searchByCategory.php";
    private static String url_search_keyword = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/searchByKeyword.php";
    private static String url_search_location = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/searchByLocation.php";

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_search);
        conn = (DatabaseConnection)getApplicationContext();

        intent = getIntent();

        // Selects what type of search to do
        String category = intent.getStringExtra("searchCategory");
        String location = intent.getStringExtra("searchLocation");

        order = "tour_Name";
        by = "ASC";

        categorySelectedSpinner = "All";

        initSearchView();
        registerClickCallback();

        Spinner spinner = (Spinner) findViewById(R.id.sortBy);
        // Create an ArrayAdapter using the string array and a default spinner layout
        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(this,
                R.array.sort_by_array, android.R.layout.simple_spinner_item);
        // Specify the layout to use when the list of choices appears
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

        spinner.setOnItemSelectedListener(SearchActivity.this);
        // Apply the adapter to the spinner
        spinner.setAdapter(adapter);
    }

    @Override
    protected void onNewIntent(Intent intent) {
        super.onNewIntent(intent);
        setIntent(intent);
        this.intent = intent;

        tourInfo.clear();

        String category = intent.getStringExtra("searchCategory");
        String location = intent.getStringExtra("searchLocation");

        order = "tour_Name";
        by = "ASC";

        categorySelectedSpinner = "All";

        initSearchView();
        registerClickCallback();

        Spinner spinner = (Spinner) findViewById(R.id.sortBy);
        // Create an ArrayAdapter using the string array and a default spinner layout
        ArrayAdapter<CharSequence> adapter = ArrayAdapter.createFromResource(this,
                R.array.sort_by_array, android.R.layout.simple_spinner_item);
        // Specify the layout to use when the list of choices appears
        adapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);
        // Apply the adapter to the spinner
        spinner.setAdapter(adapter);
    }

    /**
     * Search tours by category
     * @param intent
     */
    private void handleCategoryIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        findViewById(R.id.categorySpinner).setVisibility(View.GONE);
        findViewById(R.id.button12).setVisibility(View.GONE);

        query = intent.getStringExtra("category");
        new LoadByCategory().execute();
    }

    /**
     * Search tours by category
     * @param intent
     */
    private void handleLocationIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);

        countryQuery = intent.getStringExtra("country");
        stateQuery = intent.getStringExtra("state");
        cityQuery = intent.getStringExtra("city");

        new LoadByLocation().execute();
    }

    /**
     * Search tours by keyword
     * @param intent
     */
    private void handleIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        ArrayList<Integer> indexes;
        TourClass tour;

        if (Intent.ACTION_SEARCH.equals(intent.getAction())) {
            query = intent.getStringExtra(SearchManager.QUERY);
            new LoadByKeyword().execute();
        }
    }

    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_main, menu);
        this.menu = menu;
        if (conn.isLogged())
        {
            menu.findItem(R.id.account).setVisible(true);
            menu.findItem(R.id.signout).setVisible(true);
        } else {
            menu.findItem(R.id.account).setVisible(false);
            menu.findItem(R.id.signout).setVisible(false);
        }
        return true;
    }

    /**
     * Starts search manager
     */
    private void initSearchView() {
        SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        final SearchView searchView = (SearchView) findViewById(R.id.searchView);
        SearchableInfo searchableInfo = searchManager.getSearchableInfo(getComponentName());
        searchView.setSearchableInfo(searchableInfo);
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {
        // Handle action bar item clicks here. The action bar will
        // automatically handle clicks on the Home/Up button, so long
        // as you specify a parent activity in AndroidManifest.xml.
        switch (item.getItemId()) {
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
                finish();
                return true;

        }

        return super.onOptionsItemSelected(item);
    }

    /**
     * Calls tourist account activity
     */
    public void account() {
        Intent intent = new Intent(this, AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    /**
     * Click listener for search results
     */
    private void registerClickCallback() {
        ListView list = (ListView) findViewById(R.id.listView);
        list.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View viewClicked,
                                    int position, long id) {
                Tour clickedTour = tourInfo.get(position);
                Intent i = new Intent(getApplicationContext(), TourPageActivity.class);
                i.putExtra("tourId",clickedTour.getId());
                startActivity(i);
            }
        });
    }

    /**
     * Selects what type of sorting to do
     * @param parent
     * @param view
     * @param position
     * @param id
     */
    @Override
    public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
        switch (position) {
            // A-Z
            case 0:
                tourInfo.clear();
                order = "tour_Name";
                by = "ASC";
                callSearch();
                break;
            // Z-A
            case  1:
                tourInfo.clear();
                order = "tour_Name";
                by = "DESC";
                callSearch();
                break;
            // Lowest Price
            case 2:
                tourInfo.clear();
                order = "Price";
                by = "ASC";
                callSearch();
                break;
            // Highest Price
            case 3:
                tourInfo.clear();
                order = "Price";
                by = "DESC";
                callSearch();
                break;
            // Lowest Extremeness
            case 4:
                tourInfo.clear();
                order = "extremeness";
                by = "ASC";
                callSearch();
                break;
            // Highest Extremeness
            case 5:
                tourInfo.clear();
                order = "extremeness";
                by = "DESC";
                callSearch();
                break;
            // Lowest Rating
            case 6:
                tourInfo.clear();
                order = "avg";
                by = "ASC";
                callSearch();
                break;
            // Highest Rating
            case 7:
                tourInfo.clear();
                order = "avg";
                by = "DESC";
                callSearch();
                break;
        }
    }

    /**
     * Starts selected search method
     */
    private void callSearch() {
        String category = intent.getStringExtra("searchCategory");
        String location = intent.getStringExtra("searchLocation");

        // Choose search method
        if(category == null) {
            selectedCategory = false;
        } else {
            selectedCategory = category.equals("true");
        }

        if(location == null) {
            selectedLocation = false;
        } else {
            selectedLocation = location.equals("true");
        }

        if(selectedCategory) {
            handleCategoryIntent(intent);
        } else if (selectedLocation) {
            handleLocationIntent(intent);
        } else {
            handleIntent(intent);
        }
    }

    /**
     * Refines search by category
     * @param view
     */
    public void refine(View view) {
        tourInfo.clear();
        categorySelectedSpinner = cSpinner.getSelectedItem().toString();
        callSearch();
    }

    @Override
    public void onNothingSelected(AdapterView<?> parent) {

    }

    /**
     * Checks if link is active
     * @param urlString
     * @return
     * @throws java.net.MalformedURLException
     * @throws java.io.IOException
     */
    public static int getResponseCode(String urlString) throws MalformedURLException, IOException {
        URL u = new URL(urlString);
        HttpURLConnection huc =  (HttpURLConnection)  u.openConnection();
        huc.setRequestMethod("GET");
        huc.setRequestProperty("User-Agent", "Mozilla/5.0 (Windows; U; Windows NT 6.0; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 (.NET CLR 3.5.30729)");
        huc.connect();
        return huc.getResponseCode();
    }

    /**
     * Fills the listview with query results
     */
    private class MyListAdapter extends ArrayAdapter<Tour> {

        public MyListAdapter() {
            super(SearchActivity.this, R.layout.result_list, tourInfo);

        }

        public View getView(int position, View convertView, ViewGroup parent) {
            View itemView = convertView;
            if (itemView == null) {
                itemView = getLayoutInflater().inflate(R.layout.result_list, parent, false);

            }

            // find the list
            Tour currentTour = tourInfo.get(position);

            // fill the view
            //int draw = getResources().getIdentifier(currentTour.getPictures().get(0),"drawable",getPackageName());

            picture = (ImageView) itemView.findViewById(R.id.tourPic);
            picture.setImageBitmap(currentTour.getPictures().get(0));

            RatingBar aRating = (RatingBar) itemView.findViewById(R.id.avgRate);
            aRating.setRating((float) currentTour.getAvg());

            RatingBar eRating = (RatingBar) itemView.findViewById(R.id.tourRating);
            eRating.setRating((float) currentTour.getExtremeness());

            TextView tName = (TextView) itemView.findViewById(R.id.tourName);
            tName.setText(currentTour.getName());

            TextView tPrice = (TextView) itemView.findViewById(R.id.tourPrice);
            tPrice.setText("$" + String.format("%.2f", currentTour.getPrice()));



            return itemView;
        }
    }

    /**
     * Search database with results by categories
     */
    class LoadByCategory extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(SearchActivity.this);
            pDialog.setMessage(getString(R.string.loading));
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
                categoryName.add(new BasicNameValuePair("category", query));
                categoryName.add(new BasicNameValuePair("order", order));
                categoryName.add(new BasicNameValuePair("by", by));

                HttpPost httppost = new HttpPost(url_search_categories);

                httppost.setEntity(new UrlEncodedFormEntity(categoryName, "UTF-8"));

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
                backup = jObj.getJSONArray("tours"); // Get tours

                for (int i=0; i<backup.length(); i++) {
                    JSONObject c = backup.getJSONObject(i);
                    ArrayList<Bitmap> pictures = new ArrayList<>();

                    try {
                        BitmapFactory.Options options = new BitmapFactory.Options();
                        options.inJustDecodeBounds = true;
                        // Calculate inSampleSize
                        options.inSampleSize = 5;
                        // Decode bitmap with inSampleSize set
                        options.inJustDecodeBounds = false;

                        bitmap = BitmapFactory.decodeStream((InputStream) new URL(c.getString(TAG_PHOTO).trim() + "1.jpg").getContent(), null, options);
                        pictures.add(bitmap);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }

                    success = jObj.getInt("success");

                    // Adds tour information from database to Tour class
                    tourInfo.add(new Tour(c.getString(TAG_NAME), Price.getDouble(c.getString(TAG_PRICE)),
                            new ArrayList<>(Arrays.asList(bitmap)), Integer.parseInt(c.getString(TAG_KEY)),
                            Double.parseDouble(c.getString(TAG_EXTREMENESS)),c.getDouble(TAG_AVG)));
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
                    if(success == 0) {
                        findViewById(R.id.result).setVisibility(View.VISIBLE);
                        findViewById(R.id.refiner).setVisibility(View.GONE);
                    } else {
                        findViewById(R.id.result).setVisibility(View.GONE);
                        findViewById(R.id.refiner).setVisibility(View.VISIBLE);
                    }
                    ArrayAdapter<Tour> adapter = new MyListAdapter();

                    listView = (ListView) findViewById(R.id.listView);
                    listView.setAdapter(adapter);
                }
            });
        }

    }

    /**
     * Search database with results by categories
     */
    class LoadByLocation extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(SearchActivity.this);
            pDialog.setMessage(getString(R.string.loading));
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
                categoryName.add(new BasicNameValuePair("country", countryQuery));
                categoryName.add(new BasicNameValuePair("state", stateQuery));
                categoryName.add(new BasicNameValuePair("city", cityQuery));
                categoryName.add(new BasicNameValuePair("order", order));
                categoryName.add(new BasicNameValuePair("by", by));
                categoryName.add(new BasicNameValuePair("cat_refine", categorySelectedSpinner));


                HttpPost httppost = new HttpPost(url_search_location);

                httppost.setEntity(new UrlEncodedFormEntity(categoryName, "UTF-8"));

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
                backup = jObj.getJSONArray("tours");

                for (int i=0; i<backup.length(); i++) {
                    JSONObject c = backup.getJSONObject(i);
                    ArrayList<Bitmap> pictures = new ArrayList<>();
                    try {

                        BitmapFactory.Options options = new BitmapFactory.Options();
                        options.inJustDecodeBounds = true;
                        // Calculate inSampleSize
                        options.inSampleSize = 5;
                        // Decode bitmap with inSampleSize set
                        options.inJustDecodeBounds = false;

                        bitmap = BitmapFactory.decodeStream((InputStream) new URL(c.getString(TAG_PHOTO).trim() + "1.jpg").getContent(), null, options);
                        pictures.add(bitmap);

                    } catch (Exception e) {
                        e.printStackTrace();
                    }

                    tourInfo.add(new Tour(c.getString(TAG_NAME), Price.getDouble(c.getString(TAG_PRICE)),
                            new ArrayList<>(Arrays.asList(bitmap)), Integer.parseInt(c.getString(TAG_KEY)),
                            Double.parseDouble(c.getString(TAG_EXTREMENESS)),c.getDouble(TAG_AVG)));
                }

                success = jObj.getInt("success");

                backupCat = jObj.getJSONArray("categories");

                categories.clear();
                categories.add("All");

                for (int i=0; i<backupCat.length(); i++) {
                    JSONObject c = backupCat.getJSONObject(i);

                    if(!categories.contains(c.getString(TAG_CAT_NAME))) {
                        categories.add(c.getString(TAG_CAT_NAME));
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
                    if(success == 0) {
                        findViewById(R.id.result).setVisibility(View.VISIBLE);
                        findViewById(R.id.refiner).setVisibility(View.GONE);
                    } else {
                        findViewById(R.id.result).setVisibility(View.GONE);
                        findViewById(R.id.refiner).setVisibility(View.VISIBLE);
                    }
                    ArrayAdapter<Tour> adapter = new MyListAdapter();

                    cSpinner = (Spinner) findViewById(R.id.categorySpinner);

                    cAdapter = new ArrayAdapter<>(SearchActivity.this, android.R.layout.simple_spinner_item, categories);
                    cAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

                    /*cSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                        @Override
                        public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                            tourInfo.clear();
                            categorySelectedSpinner = parent.getSelectedItem().toString();
                            System.out.println("Category Selected " +  categorySelectedSpinner);
                            callSearch();
                        }

                        @Override
                        public void onNothingSelected(AdapterView<?> parent) {

                        }
                    });*/

                    cSpinner.setAdapter(cAdapter);

                    listView = (ListView) findViewById(R.id.listView);
                    listView.setAdapter(adapter);
                }
            });
        }

    }

    /**
     * Search database with results by keyword
     */
    class LoadByKeyword extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(SearchActivity.this);
            pDialog.setMessage(getString(R.string.loading));
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
                categoryName.add(new BasicNameValuePair("keyword", query));
                categoryName.add(new BasicNameValuePair("order", order));
                categoryName.add(new BasicNameValuePair("by", by));
                categoryName.add(new BasicNameValuePair("cat_refine", categorySelectedSpinner));


                HttpPost httppost = new HttpPost(url_search_keyword);

                httppost.setEntity(new UrlEncodedFormEntity(categoryName, "UTF-8"));

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
                backup = jObj.getJSONArray("tours");

                for (int i=0; i<backup.length(); i++) {
                    JSONObject c = backup.getJSONObject(i);
                    ArrayList<Bitmap> pictures = new ArrayList<>();
                    try {

                        BitmapFactory.Options options = new BitmapFactory.Options();
                        options.inJustDecodeBounds = true;
                        // Calculate inSampleSize
                        options.inSampleSize = 5;
                        // Decode bitmap with inSampleSize set
                        options.inJustDecodeBounds = false;

                        bitmap = BitmapFactory.decodeStream((InputStream) new URL(c.getString(TAG_PHOTO).trim() + "1.jpg").getContent(), null, options);
                        pictures.add(bitmap);
                    } catch (Exception e) {
                        e.printStackTrace();
                    }

                    tourInfo.add(new Tour(c.getString(TAG_NAME), Price.getDouble(c.getString(TAG_PRICE)),
                            new ArrayList<>(Arrays.asList(bitmap)), Integer.parseInt(c.getString(TAG_KEY)),
                            Double.parseDouble(c.getString(TAG_EXTREMENESS)),c.getDouble(TAG_AVG)));
                }

                success = jObj.getInt("success");

                backupCat = jObj.getJSONArray("categories");

                categories.clear();
                categories.add("All");

                for (int i=0; i<backupCat.length(); i++) {
                    JSONObject c = backupCat.getJSONObject(i);

                    if(!categories.contains(c.getString(TAG_CAT_NAME))) {
                        categories.add(c.getString(TAG_CAT_NAME));
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

                    if(success == 0) {
                        findViewById(R.id.result).setVisibility(View.VISIBLE);
                        findViewById(R.id.refiner).setVisibility(View.GONE);
                    } else {
                        findViewById(R.id.result).setVisibility(View.GONE);
                        findViewById(R.id.refiner).setVisibility(View.VISIBLE);
                    }
                    ArrayAdapter<Tour> adapter = new MyListAdapter();

                    cSpinner = (Spinner) findViewById(R.id.categorySpinner);

                    cAdapter = new ArrayAdapter<>(SearchActivity.this, android.R.layout.simple_spinner_item, categories);
                    cAdapter.setDropDownViewResource(android.R.layout.simple_spinner_dropdown_item);

                    /*cSpinner.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
                        @Override
                        public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
                            tourInfo.clear();
                            categorySelectedSpinner = parent.getSelectedItem().toString();
                            System.out.println("Category Selected " +  categorySelectedSpinner);
                            callSearch();
                        }

                        @Override
                        public void onNothingSelected(AdapterView<?> parent) {

                        }
                    });*/

                    cSpinner.setAdapter(cAdapter);

                    listView = (ListView) findViewById(R.id.listView);
                    listView.setAdapter(adapter);
                }
            });
        }

    }
}
