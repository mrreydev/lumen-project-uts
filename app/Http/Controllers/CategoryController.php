<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;

class CategoryController extends Controller
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
     * Get Categories Function
     * @return categories
     */
    public function index(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $categories = Category::orderBy('id', 'ASC')->paginate()->toArray();
            
            if ($acceptHeader === 'application/json') {
                $response = [
                    'message' => 'Get Categories Success',
                    'status_code' => Response::HTTP_OK,
                    'data' => [
                        'total' => $categories['total'],
                        'limit' => $categories['per_page'],
                        'pagination' => [
                            'next_page' => $categories['next_page_url'],
                            'prev_page' => $categories['prev_page_url'],
                            'current_page' => $categories['current_page']
                        ],
                        'data' => $categories['data']
                    ]
                ];
    
                return response()->json($response, Response::HTTP_OK);
            } else {
                $xml = new \SimpleXMLElement('<categories/>');

                foreach ($categories['data'] as $item) {
                    // create xml
                    $xmlItem = $xml->addChild('category');

                    $xmlItem->addChild('id', $item['id']);
                    $xmlItem->addChild('name', $item['name']);
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
     * Create Category Function
     */
    public function create(Request $request)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {

            $input = $request->all();

            $validationRules = [
                'name' => 'required|string|max:200'
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }

            $category = new Category();

            $category->name = $input['name'];

            if ($category->save()) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Create Category Success',
                        'status_code' => Response::HTTP_CREATED,
                        'data' => $category
                    ];
        
                    return response()->json($response, Response::HTTP_CREATED);
                } else {
                    $xml = new \SimpleXMLElement('<category/>');

                    $xml->addChild('id', $category->id);
                    $xml->addChild('name', $category->name);

                    return $xml->asXML();
                }
            } else {
                $response = [
                    'message' => 'Create Categoty Failed',
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
     * Show Category Function
     */
    public function show(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $category = Category::find($id);

            if (isset($category)) {

                if ($acceptHeader === 'application/json') {
                    $response = [
                        'message' => 'Get Category Success',
                        'status_code' => Response::HTTP_OK,
                        'data' => $category
                    ];
        
                    return response()->json($response, Response::HTTP_OK);
                } else {
                    $xml = new \SimpleXMLElement('<category/>');

                    $xml->addChild('id', $category->id);
                    $xml->addChild('name', $category->name);

                    return $xml->asXML();
                }

            } else {
                $response = [
                    'message' => 'Category Not Found',
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
     * Update Category Function
     */
    public function update(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $input = $request->all();

            $validationRules = [
                'name' => 'required|string|max:200'
            ];

            $validator = Validator::make($input, $validationRules);

            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_BAD_REQUEST);
            }
            
            $category = Category::find($id);

            if (isset($category)) {
                $category->name = $input['name'];

                if ($category->save()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Update Category Success',
                            'status_code' => Response::HTTP_OK,
                            'data' => $category
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<category/>');

                        $xml->addChild('id', $category->id);
                        $xml->addChild('name', $category->name);

                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Create Categoty Failed',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
            
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Category Not Found',
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
     * Delete Category Function
     */
    public function delete(Request $request, $id)
    {
        $acceptHeader = $request->header('Accept');

        if ($acceptHeader === 'application/json' || $acceptHeader === 'application/xml') {
            $category = Category::find($id);

            if (isset($category)) {
                if ($category->delete()) {

                    if ($acceptHeader === 'application/json') {
                        $response = [
                            'message' => 'Delete Category Success',
                            'status_code' => Response::HTTP_OK
                        ];
            
                        return response()->json($response, Response::HTTP_OK);
                    } else {
                        $xml = new \SimpleXMLElement('<category/>');

                        $xml->addChild('message', 'Delete Category Success');
                        $xml->addChild('status_code', Response::HTTP_OK);

                        return $xml->asXML();
                    }
                } else {
                    $response = [
                        'message' => 'Delete Category Success',
                        'status_code' => Response::HTTP_INTERNAL_SERVER_ERROR
                    ];
        
                    return response()->json($response, Response::HTTP_INTERNAL_SERVER_ERROR);
                }
            } else {
                $response = [
                    'message' => 'Category Not Found',
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
