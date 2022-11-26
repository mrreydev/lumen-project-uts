<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get Products Function
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');
        
        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $products = Product::with('category')->where('user_id', Auth::user()->id)->paginate()->toArray();
            // dd($products);
            if ($acceptHeader === 'application/json') {
                $response = [       
                    'message' => 'Get Products Success',
                    'status_code' => Response::HTTP_OK,
                    'data' => [
                        'total' => $products['total'],
                        'limit' => $products['per_page'],
                        'pagination' => [
                            'next_page' => $products['next_page_url'],
                            'prev_page' => $products['prev_page_url'],
                            'current_page' => $products['current_page']
                        ],
                        'data' => $products['data']
                    ]
                ];
    
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<products/>');

                foreach ($products['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('product');

                    $xmlItem->addChild('id', $item['id']);
                    $xmlItem->addChild('name', $item['name']);
                    $xmlItem->addChild('description', $item['description']);
                    $xmlItem->addChild('stock', $item['stock']);
                    $xmlItem->addChild('price', $item['price']);
                }

                return $xml->asXML();
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Create Product Function
     */
    public function create(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $input = $request->all();
            $input['user_id'] = Auth::user()->id;

            $validationRules = [
                'name' => 'required|string|max:300',
                'description' => 'required|string|max:500',
                'stock' => 'required|integer',
                'price' => 'required|integer',
                'category_id' => 'required|integer',
                'user_id' => 'required|integer'
            ];
            
            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            $product = new Product();

            $product->name = $input['name'];
            $product->description = $input['description'];
            $product->stock = $input['stock'];
            $product->price = $input['price'];
            $product->category_id = $input['category_id'];
            $product->user_id = $input['user_id'];

            if ($product->save()) {
                $product->category;

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Create Product Success',
                        'status_code' => Response::HTTP_CREATED,
                        'data' => $product
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<product/>');

                    $xml->addChild('id', $product->id);
                    $xml->addChild('name', $product->name);
                    $xml->addChild('description', $product->description);
                    $xml->addChild('stock', $product->stock);
                    $xml->addChild('price', $product->price);

                    return $xml->asXML();
                }
            } else {
                $response = [
                    'message' => 'Create Product Failed',
                    'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                ];
        
                return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Show Product Function
     */
    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $product = Product::with('category')->find($id);

            if ($product) {
                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Product Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $product
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<product/>');

                    $xml->addChild('id', $product->id);
                    $xml->addChild('name', $product->name);
                    $xml->addChild('description', $product->description);
                    $xml->addChild('stock', $product->stock);
                    $xml->addChild('price', $product->price);

                    return $xml->asXML();
                }
            } else {
                $response = [
                    'message' => 'Product Not Found',
                    'status_code' => Response::HTTP_NOT_FOUND
                ];
        
                return response()->json($response, Response::HTTP_NOT_FOUND);
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Update Product Function
     */
    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $product = Product::find($id);

            if ($product) {
                $input = $request->all();
                $input['user_id'] = Auth::user()->id;

                $validationRules = [
                    'name' => 'required|string|max:300',
                    'description' => 'required|string|max:500',
                    'stock' => 'required|integer',
                    'price' => 'required|integer',
                    'category_id' => 'required|integer',
                    'user_id' => 'required|integer'
                ];
                
                $validator = Validator::make($input, $validationRules);

                if ($validator->fails()) {
                    return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
                }

                $product->name = $input['name'];
                $product->description = $input['description'];
                $product->stock = $input['stock'];
                $product->price = $input['price'];
                $product->category_id = $input['category_id'];
                $product->user_id = $input['user_id'];

                if ($product->save()) {
                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Update Product Success',
                            'status_code' => Response::HTTP_OK,
                            'data' => $product
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<product/>');
    
                        $xml->addChild('id', $product->id);
                        $xml->addChild('name', $product->name);
                        $xml->addChild('description', $product->description);
                        $xml->addChild('stock', $product->stock);
                        $xml->addChild('price', $product->price);
    
                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Update Product Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Product Not Found',
                    'status_code' => Response::HTTP_NOT_FOUND
                ];
        
                return response()->json($response, Response::HTTP_NOT_FOUND);
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }

    /**
     * Delete Product Function
     */
    public function delete(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            
            $product = Product::find($id);

            if ($product) {
                if ($product->delete()) {
                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Delete Product Success',
                            'status_code' => Response::HTTP_OK,
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<product/>');
    
                        $xml->addChild('message', 'Delete Product Success');
                        $xml->addChild('status_code', Response::HTTP_CREATED);
    
                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Delete Product Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Product Not Found',
                    'status_code' => Response::HTTP_NOT_FOUND
                ];
        
                return response()->json($response, Response::HTTP_NOT_FOUND);
            }
        } else {
            $response = [
                'message' => 'Not Acceptable',
                'status_code' => Response::HTTP_NOT_ACCEPTABLE
            ];
    
            return response()->json($response, Response::HTTP_NOT_ACCEPTABLE);
        }
    }
}
