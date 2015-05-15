package com.kiwiteam.nomiddleman;

import android.graphics.Bitmap;
import android.os.Bundle;
import android.support.v4.app.Fragment;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;

import java.util.ArrayList;

public class ImageDetailFragment extends Fragment {
    private static final String IMAGE_DATA_EXTRA = "resId";
    private static ArrayList<Bitmap> pics;
    private int mImageNum;
    private ImageView mImageView;

    private static Bitmap bitmap;
    //private ArrayList<Bitmap> pics = new ArrayList<>();

    static ImageDetailFragment newInstance(Bitmap pictures) {
        final ImageDetailFragment f = new ImageDetailFragment();
        final Bundle args = new Bundle();
        f.setArguments(args);
        bitmap = pictures;

        return f;
    }

    // Empty constructor, required as per Fragment docs
    public ImageDetailFragment() {}

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        mImageNum = getArguments() != null ? getArguments().getInt(IMAGE_DATA_EXTRA) : -1;
    }

    @Override
    public View onCreateView(LayoutInflater inflater, ViewGroup container,
                             Bundle savedInstanceState) {
        // image_detail_fragment.xml contains just an ImageView
        final View v = inflater.inflate(R.layout.image_detail_fragment, container, false);
        mImageView = (ImageView) v.findViewById(R.id.imageView);
        return v;
    }

    @Override
    public void onActivityCreated(Bundle savedInstanceState) {
        super.onActivityCreated(savedInstanceState);

        mImageView.setImageBitmap(bitmap); // Load image into ImageView
    }
}