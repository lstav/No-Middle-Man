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
import android.widget.ListView;

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


public class CategoriesActivity extends ActionBarActivity {

    private ProgressDialog pDialog;

    private DatabaseConnection conn;
    private ListView listV;
    private static String url_all_categories = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/getCategories.php";

    // JSON Node names
    private static final String TAG_SUCCESS = "success";
    private static final String TAG_CATEGORIES = "categories";
    private static final String TAG_KEY = "cat_key";
    private static final String TAG_NAME = "Category_Name";

    JSONArray category = null;
    ArrayList<String> catArray = new ArrayList<>();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_categories);

        conn = (DatabaseConnection)getApplicationContext();

        handleIntent(getIntent());
        registerClickCallback();
    }

    protected void onNewIntent(Intent intent) {
        setIntent(intent);
        handleIntent(intent);
        registerClickCallback();
    }

    /**
     * Calls class to get categories from database
     * @param intent
     */
    private void handleIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        listV = (ListView) findViewById(R.id.listView);
        new LoadAllCategories().execute();
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
     * Adds click listener to listview
     */
    private void registerClickCallback() {
        ListView list = (ListView) findViewById(R.id.listView);
        list.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> parent, View viewClicked,
                                    int position, long id) {

                String clickedCategory = catArray.get(position);

                Intent i = new Intent(getApplicationContext(), SearchActivity.class);

                i.putExtra("searchCategory","true");
                i.putExtra("category", clickedCategory);

                startActivity(i);
            }
        });
    }

    /**
     * Gets all categories from database
     */
    class LoadAllCategories extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(CategoriesActivity.this);
            //pDialog.setMessage("Loading categories. Please wait...");
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
                HttpGet httpGet = new HttpGet(url_all_categories);

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
                category = jObj.getJSONArray("categories");

                for (int i=0; i<category.length(); i++) {
                    JSONObject c = category.getJSONObject(i);
                    catArray.add(c.getString(TAG_NAME));
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
                    ArrayAdapter<String> adapter = new ArrayAdapter(CategoriesActivity.this,
                            R.layout.result_list_categories, R.id.category, catArray);
                    listV.setAdapter(adapter);
                }
            });
        }
    }


}
