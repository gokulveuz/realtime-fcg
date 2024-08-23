<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\Option;
use App\Models\OptionValue;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProController extends Controller
{

    public function index()
    {
        $products  = Product::latest()->get();
        $options = Option::latest()->get();
        return view('prod', compact('products', 'options'));
    }

    public function addPro(Request $request)
    {
        DB::transaction(function () use ($request) {
            $productId = DB::table('products')->insertGetId([
                'product_name' => $request->input('product_name'),
                'product_id' => rand(10000, 20000)
            ]);

            $skus = [];
            $skuValues = [];

            foreach ($request->input('options') as $optionIndex => $option) {
                $optionId = DB::table('options')->insertGetId([
                    'product_id' => $productId,
                    'option_name' => $option['name'],
                ]);

                $optionValues = [];
                foreach ($option['values'] as $value) {
                    $valueId = DB::table('option_values')->insertGetId([
                        'product_id' => $productId,
                        'option_id' => $optionId,
                        'value_name' => $value,
                    ]);
                    $optionValues[] = $valueId;
                }

                $newSkus = [];
                if (empty($skus)) {
                    foreach ($optionValues as $valueId) {
                        $skuId = \Str::random(8);
                        $skus[] = ['product_id' => $productId, 'sku_id' => $skuId, 'sku' => $skuId, 'price' => 443];
                        $skuValues[] = [
                            'product_id' => $productId,
                            'sku_id' => $skuId,
                            'option_id' => $optionId,
                            'value_id' => $valueId,
                        ];
                    }
                } else {
                    foreach ($skus as $sku) {
                        foreach ($optionValues as $valueId) {
                            $skuId = \Str::random(8);
                            $newSkus[] = ['product_id' => $productId, 'sku_id' => $skuId, 'sku' => $skuId, 'price' => 443];
                            $skuValues[] = [
                                'product_id' => $productId,
                                'sku_id' => $skuId,
                                'option_id' => $optionId,
                                'value_id' => $valueId,
                            ];
                        }
                    }
                    $skus = $newSkus;
                }
            }

            DB::table('product_skus')->insert($skus);
            DB::table('sku_values')->insert($skuValues);
        });

        return redirect()->back()->with('success', 'Product added successfully.');
    }

    public function show($productId)
    {
        $product = Product::with(['options.optionValues', 'skus.skuValues.option'])->findOrFail($productId);
        return view('pro.show', compact('product'));
    }

    public function getSku(Request $request, $productId)
    {

        $product = Product::findOrFail($productId);

        $sku = $product->skus()
            ->whereHas('skuValues', function ($query) use ($request) {
                foreach ($request->all() as $optionId => $valueId) {
                    $query->where('option_id', $optionId)->where('value_id', $valueId);
                }
            }, '=', count($request->all())) // Ensure that the SKU has all selected options
            ->first();

        if ($sku) {
            return response()->json([
                'sku' => $sku->sku,
                'price' => $sku->price
            ]);
        }

        return response()->json(['message' => 'No SKU found for the selected options'], 422);
    }

    public function store(Request $request)
    {
        $product = Product::create([
            'product_name' => $request->input('product_name'),
        ]);

        // Handle existing options
        if ($request->has('options')) {
            foreach ($request->input('options') as $optionId) {
                $product->options()->attach($optionId);
            }
        }

        // Handle new options
        if ($request->has('new_option_name') && $request->input('new_option_name')) {
            $newOption = Option::create([
                'option_name' => $request->input('new_option_name'),
                'product_id' => $product->product_id,
            ]);

            // Handle new option values
            if ($request->has('new_option_values')) {
                foreach ($request->input('new_option_values') as $valueName) {
                    OptionValue::create([
                        'value_name' => $valueName,
                        'option_id' => $newOption->option_id,
                    ]);
                }
            }
        }

        return redirect()->route('products.index')->with('success', 'Product added successfully!');
    }

    public function getAttribute()
    {
        dd(34);
        $search_value = request()->search_value;
        $attribute = Attribute::when($search_value, function ($q) use ($search_value) {
            $q->where('name', 'LIKE', '%' . $search_value . '%');
        })->get();

        dd($attribute);

        $view = view('attributeview', [
            'attribute' => $attribute,
            'search_value' => $search_value
        ]);

        return response()->json(['view' => $view]);
    }
}
