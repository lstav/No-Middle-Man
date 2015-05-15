package com.kiwiteam.nomiddleman;

import android.app.ProgressDialog;
import android.app.SearchManager;
import android.app.SearchableInfo;
import android.content.Context;
import android.content.Intent;
import android.os.AsyncTask;
import android.os.Bundle;
import android.support.v7.app.ActionBarActivity;
import android.support.v7.widget.SearchView;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.EditText;
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


public class ResetRequestActivity extends ActionBarActivity {

    DatabaseConnection conn;
    private static final String TAG_SUCCESS = "success";
    private static final String TAG_KEY = "key";
    private ProgressDialog pDialog;
    private static String url_request_change_password = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/requestPasswordChange.php";
    private JSONArray backup;

    private int success = 0;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_reset_request);
        conn = (DatabaseConnection)getApplicationContext();
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_login, menu);
        SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        SearchView searchView = (SearchView) menu.findItem(R.id.action_search).getActionView();
        SearchableInfo searchableInfo = searchManager.getSearchableInfo(getComponentName());
        searchView.setSearchableInfo(searchableInfo);
        return true;
    }

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {

        Intent intent;
        switch (item.getItemId()) {
            case R.id.action_cart:
                intent = new Intent(this, ShoppingCartActivity.class);
                startActivity(intent);
                return true;
            case R.id.home:
                intent = new Intent(this, MainActivity.class);
                intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                startActivity(intent);
                return true;
        }
        return super.onOptionsItemSelected(item);
    }

    // Executes login query
    public void login(View view) {
        new Reset().execute();
    }

    class Reset extends AsyncTask<String, String, String> {

        /**
         * Calls query to login
         * @return success
         */
        protected String doInBackground(String... params) {
            String result = "";

            try {
                HttpClient httpClient = new DefaultHttpClient();
                String url;

                EditText emailText = (EditText) findViewById(R.id.email);

                String email = emailText.getText().toString();

                List<NameValuePair> categoryName = new ArrayList<>();
                categoryName.add(new BasicNameValuePair("t_Email", email));

                HttpPost httppost = new HttpPost(url_request_change_password);

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

            } catch (JSONException e) {
                e.printStackTrace();
            }
            return null;
        }

        /**
         * If success = 1, the user is logged in and returned to previous activity
         * If success = 0, the user is given a "Wrong Login" message
         * @param file_url
         */
        protected void onPostExecute(String file_url) {
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    if(success == 1) {
                        Toast.makeText(getApplicationContext(), R.string.notification_sent, Toast.LENGTH_SHORT).show();
                        finish();
                    } else {
                        Toast.makeText(getApplicationContext(), R.string.not_change_password, Toast.LENGTH_SHORT).show();
                    }
                }
            });
        }



    }


}
