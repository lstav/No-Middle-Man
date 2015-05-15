package com.kiwiteam.nomiddleman;

import android.app.SearchManager;
import android.app.SearchableInfo;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.support.v4.app.ActivityCompat;
import android.support.v7.app.ActionBarActivity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.widget.SearchView;


public class MainActivity extends ActionBarActivity {

    public Intent intent;
    public int index = -1;


    DatabaseConnection conn;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);

        initMainView();
        initSearchView();

    }

    // Fills and selects the home page (logged in or not logged in)
    private void initMainView() {
        conn = (DatabaseConnection)getApplicationContext();

        if(conn.isLogged()) {
            // Login button
            findViewById(R.id.login_description).setVisibility(View.GONE);
            findViewById(R.id.login_button).setVisibility(View.GONE);

            // Register button
            findViewById(R.id.register_description).setVisibility(View.GONE);
            findViewById(R.id.register_button).setVisibility(View.GONE);

            // Account view button
            findViewById(R.id.account_button).setVisibility(View.VISIBLE);

            // Categories
            findViewById(R.id.categories_button).setVisibility(View.VISIBLE);
            findViewById(R.id.categories_row).setVisibility(View.GONE);

            //Locations
            findViewById(R.id.locations_button).setVisibility(View.VISIBLE);
            findViewById(R.id.locations_row).setVisibility(View.GONE);
        } else {
            // Account
            findViewById(R.id.account_button).setVisibility(View.GONE);

            // Categories
            findViewById(R.id.categories_button).setVisibility(View.GONE);

            //Locations
            findViewById(R.id.locations_button).setVisibility(View.GONE);
        }
    }

    // Starts the search service
    private void initSearchView() {
        SearchManager searchManager = (SearchManager) getSystemService(Context.SEARCH_SERVICE);
        final SearchView searchView = (SearchView) findViewById(R.id.searchView);
        SearchableInfo searchableInfo = searchManager.getSearchableInfo(getComponentName());
        searchView.setSearchableInfo(searchableInfo);
    }

    // Recreates home after resuming
    protected void onResume() {
        super.onResume();
        // Recreates action bar
        ActivityCompat.invalidateOptionsMenu(this);

        if(conn.isLogged()) {
            this.index = conn.getT_key();
            // Login
            findViewById(R.id.login_description).setVisibility(View.GONE);
            findViewById(R.id.login_button).setVisibility(View.GONE);

            // Register
            findViewById(R.id.register_description).setVisibility(View.GONE);
            findViewById(R.id.register_button).setVisibility(View.GONE);

            // Account
            findViewById(R.id.account_button).setVisibility(View.VISIBLE);

            //Categories
            findViewById(R.id.categories_button).setVisibility(View.VISIBLE);
            findViewById(R.id.categories_row).setVisibility(View.GONE);

            //Locations
            findViewById(R.id.locations_button).setVisibility(View.VISIBLE);
            findViewById(R.id.locations_row).setVisibility(View.GONE);
        }
    }


    @Override
    public boolean onCreateOptionsMenu(Menu menu) {
        // Inflate the menu; this adds items to the action bar if it is present.
        getMenuInflater().inflate(R.menu.menu_main, menu);
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

    @Override
    public boolean onOptionsItemSelected(MenuItem item) {

        switch (item.getItemId()) {
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

    // Opens tourist account page from home button
    public void account() {
        intent = new Intent(getApplicationContext(), AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    // Opens tourist account page from action bar option
    public void account(View view) {
        intent = new Intent(getApplicationContext(), AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    // Calls login activity
    public void login(View view) {
        intent = new Intent(this, LoginActivity.class);
        startActivity(intent);
    }

    // Calls register activity
    public void register(View view) {
        intent = new Intent(this, RegisterActivity.class);
        startActivity(intent);
    }

    // Calls search by category activity
    public void searchCat(View view) {
        intent = new Intent(this, CategoriesActivity.class);
        startActivity(intent);
    }

    // Calls search by location activity
    public void searchLoc(View view) {
        intent = new Intent(this, LocationsActivity.class);
        startActivity(intent);
    }

    // Opens settings
    public void settings(View view) {
        intent = new Intent(this, SettingsActivity.class);
        startActivity(intent);
    }
}
