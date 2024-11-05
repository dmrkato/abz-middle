<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\User as UserModel;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\GetUserRequest;
use App\Http\Requests\GetUserListRequest;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;

class UserController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function getList(GetUserListRequest $request): \Illuminate\Http\JsonResponse
    {
        $tableName = app(\App\Models\User::class)->getTable();
        $generalCount = \DB::table($tableName)->count(); //get total count of records in db
        $page = $request->get('page', 1);
        $count = $request->get('count', 5);

        //create next page url
        $nextUrl = null;
        if ($page * $count < $generalCount) {
            $nextPage = $page + 1;
            $queryParams = [
                'page' => $nextPage,
            ];
            if ($request->has('count')) {
                $queryParams['count'] = $request->get('count');
            }
            $nextUrl = URL::current() . '?' . http_build_query($queryParams);
        }

        //create prev page url
        $prevUrl = null;
        if ($page >= 2) {
            $prevPage = $page - 1;
            $queryParams = [
                'page' => $prevPage,
            ];
            if ($request->has('count')) {
                $queryParams['count'] = $request->get('count');
            }
            $prevUrl = URL::current() . '?' . http_build_query($queryParams);
        }

        $skip = ($page - 1) * $count;

        // return 404 code if page don't exist
        if ($skip >= $generalCount) {
            return response()->json([
                'message' => 'Page not found'
            ], 404);
        }

        $users = UserModel::skip($skip)->take($count)->get();

        $responseData = [
            'page' => (int)$page,
            'count' => (int)$generalCount,
            'total_pages' => (int)ceil($generalCount / $count),
            'total_users' => (int)$generalCount,
            'links' => [
                'next_url' => $nextUrl,
                'prev_url' => $prevUrl
            ],
            'users' => []
        ];
        foreach ($users as $user) {
            $responseData['users'][] = new UserResource($user);
        }
        return response()->json($responseData);
    }

    public function getUser(GetUserRequest $request, int $id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = UserModel::findOrFail($id);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $notFoundException) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $responseData = [
            'user' => new UserResource($user)
        ];

        return response()->json($responseData);
    }

    /**
     * @throws ValidationException
     */
    public function createUser(StoreUserRequest $request): \Illuminate\Http\JsonResponse
    {
        try {
            $request->validate([
                'email' => 'unique:users',
                'phone' => 'unique:users',
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception ) {
            return response()->json(['message' => 'User with this phone or email already exist'],409);
        }

        $image = $request->file('photo');
        $img = ImageManager::imagick()->read($image);
//        $img = Image::make($image->path());

        $ms = min($img->width(), $img->height());

        $imageFileName = md5(time()) . '.' . $image->extension();
        $storageDir = implode('/', [
            'thumbnails',
            $imageFileName[0],
            $imageFileName[1]
        ]);
        Storage::disk('public')->makeDirectory($storageDir);
        $thumbnailPath = Storage::disk('public')->path($storageDir . '/' . $imageFileName);

        $x = 0;
        $y = 0;

        if ($img->width() > $img->height()) {
            $x = (int)(($img->width() - $img->height())/2);
        } elseif ($img->width() < $img->height()) {
            $y = (int)(($img->height() - $img->width())/2);
        }

        $img->crop($ms, $ms, $x, $y);
        $img->resize(70,70, function ($constraint) {
            $constraint->aspectRatio();
        });
        $img->save($thumbnailPath);

        $user = new UserModel();
        $user->name = $request->get('name');
        $user->email = $request->get('email');
        $user->phone = $request->get('phone');
        $user->position_id = $request->get('position_id');
        $user->photo_file_path = $storageDir . '/' . $imageFileName;
        $user->save();

        return response()->json([
            'message' => 'User created',
            'user_id' => $user->id
        ], 201);
    }
}
