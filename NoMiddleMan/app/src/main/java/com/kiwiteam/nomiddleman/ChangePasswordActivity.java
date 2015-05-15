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


public class ChangePasswordActivity extends ActionBarActivity {

    private DatabaseConnection conn;
    private String oldPass;
    private String newPass;
    private String confirmPass;
    private String message;
    private ProgressDialog pDialog;
    private int index;
    private int success;

    private static final String TAG_SUCCESS = "success";

    private JSONArray response;

    private static String url_change_password = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/changePass.php";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_change_password);

        conn = (DatabaseConnection) getApplicationContext();
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

    /**
     * Opens account information activity
     */
    public void account() {
        Intent intent = new Intent(this, AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    /**
     * Calls password change and changes password in database
     * @param view
     */
    public void confirm(View view) {
        EditText nPass = (EditText) findViewById(R.id.newPass);
        EditText cNPass = (EditText) findViewById(R.id.cNewPass);
        EditText oPass = (EditText) findViewById(R.id.oldPass);

        if(nPass.getText().toString().equals(cNPass.getText().toString()) && nPass.getText().toString().trim().length() >= 8
                && nPass.getText().toString().trim().length() >= 8) {
            newPass = nPass.getText().toString();
            confirmPass = cNPass.getText().toString();

            oldPass = oPass.getText().toString();

            new PassChange().execute();
            //finish();
        } else {
            Toast.makeText(this, R.string.password_not_match, Toast.LENGTH_SHORT).show();
        }

    }

    /**
     * Returns to previous activity
     * @param view
     */
    public void cancel(View view) {
        finish();
    }

    /**
     * Sends new password to database and updates the database
     */
    class PassChange extends AsyncTask<String, String, String> {
        /*rotected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(ChangePasswordActivity.this);
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
                categoryName.add(new BasicNameValuePair("key", Integer.toString(index)));
                categoryName.add(new BasicNameValuePair("password", newPass));
                categoryName.add(new BasicNameValuePair("oldpass", oldPass));

                HttpPost httppost = new HttpPost(url_change_password);

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

                if(success == 0) {
                    message = jObj.getString("message");
                }

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
            //pDialog.dismiss();
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    if(success == 1) {
                        finish();
                    } else {
                        Toast.makeText(getApplicationContext(), R.string.wrong_pass, Toast.LENGTH_SHORT).show();
                        //Toast.makeText(getApplicationContext(), message, Toast.LENGTH_SHORT).show();
                    }
                }
            }); 
        }

    }

}
