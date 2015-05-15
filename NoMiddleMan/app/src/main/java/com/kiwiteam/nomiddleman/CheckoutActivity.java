package com.kiwiteam.nomiddleman;

import android.app.Activity;
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
import android.util.Log;
import android.view.Gravity;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.GridLayout;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.RadioGroup;
import android.widget.RelativeLayout;
import android.widget.TextView;
import android.widget.Toast;

import com.paypal.android.MEP.CheckoutButton;
import com.paypal.android.MEP.PayPal;
/*import com.paypal.android.sdk.payments.PayPalConfiguration;
import com.paypal.android.sdk.payments.PayPalPayment;
import com.paypal.android.sdk.payments.PayPalService;
import com.paypal.android.sdk.payments.PaymentActivity;
import com.paypal.android.sdk.payments.PaymentConfirmation;*/

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
import java.math.BigDecimal;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.Arrays;
import java.util.List;

import com.paypal.android.MEP.PayPalActivity;
import com.paypal.android.MEP.PayPalAdvancedPayment;
import com.paypal.android.MEP.PayPalInvoiceData;
import com.paypal.android.MEP.PayPalPayment;
import com.paypal.android.MEP.PayPalReceiverDetails;
import com.paypal.android.a.*;
import com.paypal.android.*;


public class CheckoutActivity extends ActionBarActivity {

    private DatabaseConnection conn;
    private ArrayAdapter<ShoppingItem> adapter;
    private ListView listView;
    private List<ShoppingItem> shoppingCart = new ArrayList<>();
    private List<PayPalItem> paypalitem = new ArrayList<>();

    private double totalPrice = 0.0;
    private int ts_key = -1;
    private int success = 0;
    private boolean active = true;

    private Bitmap bitmap;
    private ProgressDialog pDialog;
    private ImageView picture;

    private JSONArray backup;

    private static final String TAG_KEY = "key";
    private static final String TAG_TSKEY = "ts_key";
    private static final String TAG_NAME = "name";
    private static final String TAG_PRICE = "price";
    private static final String TAG_EXTREMENESS = "extremeness";
    private static final String TAG_PHOTO = "photo";
    private static final String TAG_QUANTITY = "quantity";
    private static final String TAG_DATE = "date";
    private static final String TAG_TIME = "time";
    private static final String TAG_ACTIVE = "isActive";
    private static final String TAG_SUCCESS = "success";
    private static final String TAG_GEMAIL = "gEmail";
    private static final String TAG_AVG = "avg";

    private static final String CONFIG_CLIENT_ID = "AdurtY7CcDo9ygeg8Ic1fhVjZuzPvW-nB4lcXGHrEuExkAWfgxaAbUEpmwMMjmALMXi-EPz-zNZJhKBz";

    /*private static PayPalConfiguration config = new PayPalConfiguration()
            // Start with mock environment.  When ready, switch to sandbox (ENVIRONMENT_SANDBOX)
            // or live (ENVIRONMENT_PRODUCTION)
            .environment(PayPalConfiguration.ENVIRONMENT_SANDBOX)
            .clientId(CONFIG_CLIENT_ID)
            .merchantName("No Middle Man");*/


    private static String url_get_checkout = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/checkout.php";
    private static String url_remove_from_cart = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/removeFromShoppingCart.php";
    private static String url_pay = "http://kiwiteam.ece.uprm.edu/NoMiddleMan/Android%20Files/pay.php";
    private CheckoutButton launchPayPalButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_checkout);

        conn = (DatabaseConnection)getApplicationContext();
        Intent intent = getIntent();


        /*Intent intent2 = new Intent(this, PayPalService.class);

        intent2.putExtra(PayPalService.EXTRA_PAYPAL_CONFIGURATION, config);

        startService(intent2);*/

        initLibrary();
        handleIntent(intent);
    }

    public void initLibrary() {
        PayPal pp = PayPal.getInstance();
        if(pp == null) {
            pp = PayPal.initWithAppID(this.getBaseContext(), "APP-80W284485P519543T", PayPal.ENV_SANDBOX);

            // Required settings:

            // Set the language for the library
            pp.setLanguage("en_US");

            // Some Optional settings:

            // Sets who pays any transaction fees. Possible values are:
            // FEEPAYER_SENDER, FEEPAYER_PRIMARYRECEIVER, FEEPAYER_EACHRECEIVER, and FEEPAYER_SECONDARYONLY
            pp.setFeesPayer(PayPal.FEEPAYER_EACHRECEIVER);

            // true = transaction requires shipping
            pp.setShippingEnabled(false);

            pp.setLibraryInitialized(true);

            //PayPal.
            //_paypalLibraryInit = true;
        }
    }

    public void addToPayPalItem(String gEmail, Double price) {
        boolean isInList = false;
        for(int i = 0; i < paypalitem.size(); i++) {
            if(paypalitem.get(i).getgEmail().equals(gEmail)) {
                paypalitem.get(i).setPrice(price);
                isInList = true;
                break;
            }
        }

        if(!isInList) {
            paypalitem.add(new PayPalItem(gEmail, price));
        }
    }

    /**
     * Listener for PayPal button
     * @param arg0
     */
    public void PayPalButtonClick(View arg0) {
    // Create a basic PayPal payment
        ArrayList<PayPalReceiverDetails> paymentList = new ArrayList<>();
        Double nomipart = 0.0;
        Double total = 0.0;

        for(int i = 0; i < paypalitem.size(); i++) {
            // Uncomment following section to run PayPal recepients for every tour guide
            /*
            PayPalReceiverDetails pay = new PayPalReceiverDetails();
            pay.setRecipient(paypalitem.get(i).getgEmail());
            pay.setMerchantName("No Middle Man");
            pay.setSubtotal(new BigDecimal(paypalitem.get(i).getPrice() - paypalitem.get(i).getPrice()*.10));
            pay.setPaymentType(PayPal.PAYMENT_TYPE_SERVICE);
            */

            total = total + paypalitem.get(i).getPrice()*.90;

            nomipart = nomipart + paypalitem.get(i).getPrice()*.10;


            //paymentList.add(pay);

        }

        PayPalReceiverDetails payment1 = new PayPalReceiverDetails();
        PayPalReceiverDetails payment2 = new PayPalReceiverDetails();

    // Set the recipient for the payment (can be a phone number)

        payment1.setRecipient("skydiving-biz@test.com");
        payment1.setMerchantName("No Middle Man");

        payment2.setRecipient("nomiddlemantest@yahoo.com");
        payment2.setMerchantName("No Middle Man");

    // Set the payment amount, excluding tax and shipping costs
        payment1.setSubtotal(new BigDecimal(total));
        payment2.setSubtotal(new BigDecimal(nomipart));

    // Set the payment type--his can be PAYMENT_TYPE_GOODS,
    // PAYMENT_TYPE_SERVICE, PAYMENT_TYPE_PERSONAL, or PAYMENT_TYPE_NONE

        payment1.setPaymentType(PayPal.PAYMENT_TYPE_SERVICE);
        payment2.setPaymentType(PayPal.PAYMENT_TYPE_SERVICE);

    // PayPalInvoiceData can contain tax and shipping amounts, and an
    // ArrayList of PayPalInvoiceItem that you can fill out.
    // These are not required for any transaction.
        //PayPalInvoiceData invoice = new PayPalInvoiceData();
        paymentList.add(payment1);
        paymentList.add(payment2);

        PayPalAdvancedPayment adv = new PayPalAdvancedPayment();
        adv.setCurrencyType("USD");

        adv.setReceivers(paymentList);
    // Set the tax amount
        //invoice.setTax(new BigDecimal(0));

        Intent checkoutIntent = PayPal.getInstance().checkout(adv, this);
                /*new Intent(this, PayPalActivity.class);
        checkoutIntent.putExtra(PayPalActivity.EXTRA_PAYMENT_INFO, payment);*/
        this.startActivityForResult(checkoutIntent,1);
    }


    protected void onNewIntent(Intent intent) {
        setIntent(intent);
        shoppingCart.clear();
        handleIntent(intent);
    }

    /**
     * Removes item from list
     * @param position
     */
    public void removeItem(int position) {

        ts_key = shoppingCart.get(position).getSessionID();

        totalPrice = totalPrice - shoppingCart.get(position).getTourPrice();

        new RemoveFromShoppingCart().execute();

        adapter.notifyDataSetChanged();

        /*conn.removeFromShoppingCart(position);
        adapter.notifyDataSetChanged();
        TextView tPrice = (TextView) findViewById(R.id.price);
        if(!adapter.isEmpty()) {
            double price = conn.getTotalPrice();
            tPrice.setText("$" + String.format("%.2f", price));

        } else {
            Intent intent = new Intent(this, MainActivity.class);
            intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
            startActivity(intent);
        }*/
    }

    /**
     * Shows objects from the shopping cart that are still active
     * @param intent
     */
    private void handleIntent(Intent intent) {
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
        new LoadCheckout().execute();

        /*shoppingCart = conn.getShoppingCart(0);

        if(!shoppingCart.isEmpty()) {

            adapter = new MyListAdapter();

            listView = (ListView) findViewById(R.id.listView);
            listView.setAdapter(adapter);

            TextView tPrice = (TextView) findViewById(R.id.price);
            double prices = 0;
            for (int i=0;i<shoppingCart.size();i++) {
                prices = prices + shoppingCart.get(i).getTourPrice();
            }
            tPrice.setText("$" + String.format("%.2f", prices));

        } else {
            TextView fName = (TextView) findViewById(R.id.result);
            fName.setText(R.string.empty_cart);

            TextView tPrice = (TextView) findViewById(R.id.price);
            tPrice.setText("$0.00");
        }*/
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

    public void account() {
        Intent intent = new Intent(this, AccountActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
    }

    public void cancel(View view) {
        Intent intent = new Intent(this, ShoppingCartActivity.class);
        intent.putExtra("Index", conn.getT_key());
        startActivity(intent);
        finish();
    }

    private class MyListAdapter extends ArrayAdapter<ShoppingItem> {

        public MyListAdapter() {
            super(CheckoutActivity.this, R.layout.checkout_item, shoppingCart);

        }

        public View getView(final int position, View convertView, ViewGroup parent) {
            View itemView = convertView;
            if (itemView == null) {
                itemView = getLayoutInflater().inflate(R.layout.checkout_item, parent, false);

            }

            itemView.findViewById(R.id.remove).setOnClickListener(new View.OnClickListener() {
                @Override
                public void onClick(View v) {
                    removeItem(position);
                }
            });

            // find the list
            ShoppingItem currentTour = shoppingCart.get(position);

            // fill the view
            //int draw = getResources().getIdentifier(currentTour.getTourPicture().get(0),"drawable",getPackageName());

            if(currentTour.getTourPicture().size() > 0) {
                picture = (ImageView) itemView.findViewById(R.id.tourPic);
                picture.setImageBitmap(currentTour.getTourPicture().get(0));
            } else {
                picture = (ImageView) itemView.findViewById(R.id.tourPic);
                picture.setImageDrawable(getResources().getDrawable(R.mipmap.ic_launcher));
            }

            TextView tName = (TextView) itemView.findViewById(R.id.tourName);
            tName.setText(currentTour.getTourName());
            //System.out.println(currentTour.getName());

            TextView tPrice = (TextView) itemView.findViewById(R.id.tourPrice);
            double price = currentTour.getTourPrice();
            tPrice.setText("$"+ String.format("%.2f", price));

            TextView tQuantity = (TextView) itemView.findViewById(R.id.quantity);
            tQuantity.setText(Integer.toString(currentTour.getQuantity()));

            TextView tDate = (TextView) itemView.findViewById(R.id.date);
            tDate.setText(currentTour.getDate());

            TextView tTime = (TextView) itemView.findViewById(R.id.time);
            tTime.setText(currentTour.getTime());

            return itemView;
        }
    }

    public void onActivityResult(int requestCode, int resultCode, Intent intent) {
        switch (resultCode) {
            // The payment succeeded
            case Activity.RESULT_OK:
                String payKey = intent.getStringExtra(PayPalActivity.EXTRA_PAY_KEY);
                new Paying().execute();
                break;

            // The payment was canceled
            case Activity.RESULT_CANCELED:
                Log.i("paymentExample", "The user canceled.");
                break;

            // The payment failed, get the error from the EXTRA_ERROR_ID and EXTRA_ERROR_MESSAGE
            case PayPalActivity.RESULT_FAILURE:
                String errorID = intent.getStringExtra(PayPalActivity.EXTRA_ERROR_ID);
                String errorMessage = intent.getStringExtra(PayPalActivity.EXTRA_ERROR_MESSAGE);
                Toast.makeText(getApplicationContext(), R.string.payment_failed, Toast.LENGTH_SHORT).show();
        }
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
     * Search database with results by keyword
     */
    class LoadCheckout extends AsyncTask<String, String, String> {

        protected void onPreExecute() {
            super.onPreExecute();
            pDialog = new ProgressDialog(CheckoutActivity.this);
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
                categoryName.add(new BasicNameValuePair("t_key", Integer.toString(conn.getT_key())));

                HttpPost httppost = new HttpPost(url_get_checkout);

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

                    backup = jObj.getJSONArray("tours");
                    ArrayList<Bitmap> pictures = new ArrayList<>();

                    for (int i=0; i<backup.length(); i++) {
                        JSONObject c = backup.getJSONObject(i);
                        try {
                            if(getResponseCode(c.getString(TAG_PHOTO).trim() + "1.jpg") != 404) {
                                BitmapFactory.Options options = new BitmapFactory.Options();
                                options.inJustDecodeBounds = true;
                                // Calculate inSampleSize
                                options.inSampleSize = 5;
                                // Decode bitmap with inSampleSize set
                                options.inJustDecodeBounds = false;

                                bitmap = BitmapFactory.decodeStream((InputStream) new URL(c.getString(TAG_PHOTO).trim() + "1.jpg").getContent(), null, options);
                                pictures.add(bitmap);
                            }
                        } catch (Exception e) {
                            e.printStackTrace();
                        }

                        boolean isActive = false;
                        if(c.getString(TAG_ACTIVE).equals("t")) {
                            isActive = true;
                        }

                        shoppingCart.add(new ShoppingItem(new Tour(c.getString(TAG_NAME),
                                Price.getDouble(c.getString(TAG_PRICE)),
                                new ArrayList<>(Arrays.asList(bitmap)),
                                c.getInt(TAG_KEY),c.getDouble(TAG_EXTREMENESS),0.0),c.getInt(TAG_TSKEY),
                                c.getInt(TAG_QUANTITY),c.getString(TAG_DATE), c.getString(TAG_TIME),
                                isActive, c.getString(TAG_GEMAIL)));

                        if(isActive) {
                            totalPrice = totalPrice + shoppingCart.get(i).getTourPrice();
                            addToPayPalItem(c.getString(TAG_GEMAIL), Price.getDouble(c.getString(TAG_PRICE)));
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
                        adapter = new MyListAdapter();

                        listView = (ListView) findViewById(R.id.listView);
                        listView.setAdapter(adapter);

                        adapter.notifyDataSetChanged();

                        /*if(!active) {
                            TextView message = (TextView) findViewById(R.id.message);
                            message.setVisibility(View.VISIBLE);
                        } else {
                            TextView message = (TextView) findViewById(R.id.message);
                            message.setVisibility(View.GONE);
                        }*/

                        TextView tPrice = (TextView) findViewById(R.id.price);
                        tPrice.setText("$" + String.format("%.2f", totalPrice));
                    } else {
                        Intent intent = new Intent(CheckoutActivity.this, MainActivity.class);
                        intent.addFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP);
                        startActivity(intent);
                        /*TextView fName = (TextView) findViewById(R.id.result);
                        findViewById(R.id.result).setVisibility(View.VISIBLE);
                        fName.setText(R.string.empty_cart);

                        findViewById(R.id.items).setVisibility(View.GONE);
                        findViewById(R.id.price).setVisibility(View.GONE);
                        findViewById(R.id.checkout).setVisibility(View.GONE);*/
                    }
                }
            });
        }

    }

    class RemoveFromShoppingCart extends AsyncTask<String, String, String> {

        @Override
        protected String doInBackground(String... params) {
            String result = "";

            try {
                HttpClient httpClient = new DefaultHttpClient();
                String url;

                List<NameValuePair> categoryName = new ArrayList<>();
                categoryName.add(new BasicNameValuePair("t_key", Integer.toString(conn.getT_key())));
                categoryName.add(new BasicNameValuePair("ts_key", Integer.toString(ts_key)));

                HttpPost httppost = new HttpPost(url_remove_from_cart);

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

        protected void onPostExecute(String file_url) {
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    adapter.clear();
                    totalPrice = 0;

                    new LoadCheckout().execute();
                }
            });
        }

    }

    class Paying extends AsyncTask<String, String, String> {

        @Override
        protected String doInBackground(String... params) {
            String result = "";

            try {
                HttpClient httpClient = new DefaultHttpClient();
                String url;

                List<NameValuePair> categoryName = new ArrayList<>();
                categoryName.add(new BasicNameValuePair("t_key", Integer.toString(conn.getT_key())));

                HttpPost httppost = new HttpPost(url_pay);

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

        protected void onPostExecute(String file_url) {
            runOnUiThread(new Runnable() {
                @Override
                public void run() {
                    Intent intent = new Intent(CheckoutActivity.this, UpcomingPurchaseHistoryActivity.class);
                    startActivity(intent);
                    finish();
                }
            });
        }

    }

    @Override
    public void onDestroy() {
        /*stopService(new Intent(this, PayPalService.class));*/
        super.onDestroy();
    }
}
