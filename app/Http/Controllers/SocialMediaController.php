<?php

namespace App\Http\Controllers;

use App\Follow;
use App\Like;
use App\Models\Donation;
use App\Post;
use App\Save;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SocialMediaController extends Controller
{
    private $MAX_RESULT_LIMIT = 15;

    public function index(Request $request)
    {
        $search = $request->get('search');

        $posts = $this->getPostsByUserId(null, null, $search);

        return view('social-media.index', compact('posts'));
    }

    public function searchUserIndex(Request $request)
    {
        $search = trim($request->get('search'));

        $users = User::select("id", "name", "profile_image")
            ->withCount([
                "organizationRole as admin_count" => function ($query) {
                    $query->whereIn("organization_roles.id", [2, 4]);
                }
            ])
            ->with("followed_users")
            ->when(filled($search), function ($query) use ($search) {
                $query->where("name", "LIKE", "%$search%");
            })
            ->orderByDesc("id")
            ->paginate($this->MAX_RESULT_LIMIT);

        $users->getCollection()->transform(function ($user) {
            return $this->getIsFollowingUser($user);
        });

        return view('social-media.search-user', compact('users'));
    }

    public function createPost()
    {
        return view('social-media.create-post');
    }

    public function toggleLike(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        try {
            $postId = $request->get("post_id");
            $userId = Auth::id();

            // check if it is liked or not
            $isLiked = Like::where("user_id", "=", $userId)
                ->where("post_id", "=", $postId)
                ->exists();

            if ($isLiked) {
                // if yes, remove the like 
                Like::where("user_id", "=", $userId)
                    ->where("post_id", "=", $postId)
                    ->delete();
            } else {
                // if not, create a new like
                Like::create([
                    "user_id" => $userId,
                    "post_id" => $postId,
                ]);
            }

            $updatedLikesCount = Post::withCount("likes")
                ->find($postId)
                ->likes_count;

            return response()->json(["updatedLikesCount" => $updatedLikesCount]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function toggleSave(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        try {
            $postId = $request->get("post_id");
            $userId = Auth::id();

            // check if it is saved or not
            $isSaved = Save::where("user_id", "=", $userId)
                ->where("post_id", "=", $postId)
                ->exists();

            if ($isSaved) {
                // if yes, remove the save 
                Save::where("user_id", "=", $userId)
                    ->where("post_id", "=", $postId)
                    ->delete();
            } else {
                // if not, create a new save
                Save::create([
                    "user_id" => $userId,
                    "post_id" => $postId,
                ]);
            }

            $updatedSavesCount = Post::withCount("saves")
                ->find($postId)
                ->saves_count;

            return response()->json(["updatedSavesCount" => $updatedSavesCount]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function addPost(Request $request)
    {
        $content = $request->get('content');
        $media = null;
        $sharedDonationId = $request->get('shared_donation_id');  // used for sharing donation posters

        if ($request->hasFile("media")) {
            $media = $request->file('media');
        }

        if (!isset($content) && !isset($media) && !isset($sharedDonationId)) {
            return response()->json(["error" => "Sila isikan sekurang-kurangnya satu input (content atau media)."]);
        }

        $this->insertPost($content, $media, null, $sharedDonationId);

        return redirect()->route('social-media.index');
    }

    public function addComment(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $postId = $request->get("post_id");
        $content = $request->get('comment');
        $media = null;

        if ($request->hasFile("media")) {
            $media = $request->file('media');
        }

        if (!isset($postId)) {
            return response()->json(["error" => "Post ID is required."]);
        }

        if (!isset($content) && !isset($media)) {
            return response()->json(["error" => "Sila isikan sekurang-kurangnya satu input (content atau media)."]);
        }

        $newComment = $this->insertPost($content, $media, $postId, null);
        $user = Auth::user();
        $userData = [
            "name" => $user->name,
            "profile_image" => $user->profile_image
        ];
        $newComment["user"] = $userData;

        return response()->json(["newComment" => $newComment]);
    }

    private function insertPost($content, $media, $postId, $sharedDonationId)
    {
        $filename = null;
        $mediaType = null;

        if (isset($media)) {
            $mediaType = explode('/', $media->getMimeType())[0];
            $filename = uniqid("media_") . "_" . str_replace(" ", "_", $media->getClientOriginalName());
            $media->move(public_path('uploads/post_media'), $filename);
        }

        $post = Post::create([
            "content" => $content,
            "media_type" => $mediaType,
            "media_url" => $filename,
            "created_at" => now(),
            "updated_at" => now(),
            "user_id" => Auth::id(),
            "post_id" => $postId,
            "shared_donation_id" => $sharedDonationId
        ]);

        return $post;
    }

    public function saves()
    {
        $posts = Post::with("user:id,name,profile_image")
            ->whereNull("post_id")
            ->withCount(["likes", "comments"])
            ->withCount([
                "likes as is_liked" => function ($query) {
                    $query->where("user_id", "=", Auth::id());
                },
                "saves as is_saved" => function ($query) {
                    $query->where("user_id", "=", Auth::id());
                }
            ])
            ->whereHas("saves", function ($query) {
                $query->where("user_id", "=", Auth::id());
            })
            ->orderByDesc("created_at")
            ->get();

        $posts->each(function ($post) {
            $post->is_liked = $post->is_liked > 0;
            $post->is_saved = $post->is_saved > 0;
        });

        return view('social-media.index', compact('posts'));
    }

    public function getCommentsByPostIdJson(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $postId = $request->get("post_id");
        $comments = $this->getCommentsByPostId($postId);

        return response()->json(["comments" => $comments]);
    }

    private function getCommentsByPostId($postId)
    {
        $comments = Post::with("user:id,name,profile_image")
            ->with("parent")
            ->whereNotNull("post_id")
            ->whereHas("parent", function ($query) use ($postId) {
                $query->where("id", "=", $postId);
            })
            ->orderBy("created_at")
            ->get();

        return $comments;
    }

    private function getIsFollowingUser($user)
    {
        // check whether a user has already been followed by the current user
        $user->is_following = $user->followers()
            ->where("follower_user_id", "=", Auth::id())
            ->exists();

        return $user;
    }

    public function profile(Request $request)
    {
        $userId = $request->get("user_id") ?? Auth::id();
        $user = User::withCount(["followers", "followed_users", "organization"])->find($userId);

        if (!isset($user)) {
            return redirect()->route('social-media.index');
        }

        $user = $this->getIsFollowingUser($user);
        $userData = [
            "id" => $user->id,
            "name" => $user->name,
            "email" => $user->email,
            "organization_count" => $user->organization_count,
            "followers_count" => $user->followers_count,
            "followed_users_count" => $user->followed_users_count,
            "is_following" => $user->is_following
        ];

        return view('social-media.profile', compact('userData'));
    }

    public function getPostsByUserIdJson(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $userId = $request->get("user_id");

        if (!isset($userId)) {
            return response()->json(["error" => "User ID required."]);
        }

        $posts = $this->getPostsByUserId($userId, null, null);

        return response()->json(["posts" => $posts]);
    }

    public function getPostsByUserId($userId, $mediaType, $searchFilter)
    {
        $posts = Post::with("user:id,name,profile_image")
            ->with("donation_post")
            ->whereNull("post_id")
            ->withCount(["likes", "comments"])
            ->withCount([
                "likes as is_liked" => function ($query) {
                    $query->where("user_id", "=", Auth::id());
                },
                "saves as is_saved" => function ($query) {
                    $query->where("user_id", "=", Auth::id());
                }
            ])
            ->when(isset($userId), function ($query) use ($userId) {
                $query->where("user_id", "=", $userId);
            })
            ->when(isset($mediaType), function ($query) use ($mediaType) {
                $query->where("media_type", "=", $mediaType);
            })
            ->when(filled($searchFilter), function ($query) use ($searchFilter) {
                $query->where("content", "LIKE", "%$searchFilter%");
            })
            ->orderByDesc("created_at")
            ->get();

        $posts->each(function ($post) {
            $post->is_liked = $post->is_liked > 0;
            $post->is_saved = $post->is_saved > 0;

            $referralCode = app(PointController::class)->getReferralCode(true, $post->user->id);

            if (isset($referralCode) && isset($post->donation_post)) {
                $post->donation_share_url = $post->donation_post->url . "?referral_code=$referralCode->code";
            }
        });

        return $posts;
    }

    public function getPostByIdJson(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $postId = $request->get("post_id");

        if (!isset($postId)) {
            return response()->json(["error" => "Post ID required."]);
        }

        $post = Post::with("user:id,name,profile_image")
            ->whereNull("post_id")
            ->withCount(["likes", "comments"])
            ->withCount([
                "likes as is_liked" => function ($query) {
                    $query->where("user_id", "=", Auth::id());
                },
                "saves as is_saved" => function ($query) {
                    $query->where("user_id", "=", Auth::id());
                }
            ])
            ->where("id", "=", $postId)
            ->orderByDesc("created_at")
            ->first();

        if (!isset($post)) {
            return response()->json(["error" => "No post found with given post ID."]);
        }

        $post->is_liked = $post->is_liked > 0;
        $post->is_saved = $post->is_saved > 0;

        return response()->json(["post" => $post]);
    }

    public function getPhotoPostsByUserIdJson(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $userId = $request->get("user_id");

        $posts = $this->getPostsByUserId($userId, "image", null);

        return response()->json(["posts" => $posts]);
    }

    public function getVideoPostsByUserIdJson(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $userId = $request->get("user_id");

        $posts = $this->getPostsByUserId($userId, "video", null);

        return response()->json(["posts" => $posts]);
    }

    public function getDonationsByUserIdJson(Request $request)
    {
        if (!$request->ajax()) {
            return;
        }

        $userId = $request->get("user_id") ?? Auth::id();

        // get the current user's organization
        $organizationIds = User::find($userId)
            ->organization()
            ->pluck("organizations.id")
            ->toArray();

        // get the donations based on the organization ids
        $donations = Donation::with("organization")
            ->whereHas("organization", function ($query) use ($organizationIds) {
                $query->whereIn("organizations.id", $organizationIds);
            })
            ->where("status", "=", 1)
            ->get();

        return response()->json(["donations" => $donations]);
    }

    public function followUser(Request $request)
    {
        try {
            if (Auth::user() == null) {
                return redirect('/login');
            }

            if (!$request->ajax()) {
                return;
            }

            $followedUserId = $request->get("followed_user_id");  // user that is being followed by current user
            $followerUserId = Auth::id();  // current user that is following others

            if (!isset($followedUserId)) {
                return response()->json(["error" => "The ID of followed user is required."]);
            }

            // check if the user is already being followed by current user
            $isFollowing = Follow::where("followed_user_id", "=", $followedUserId)
                ->where("follower_user_id", "=", $followerUserId)
                ->exists();

            if ($isFollowing) {
                // if yes, unfollow the user by deleting the follow data
                Follow::where("followed_user_id", "=", $followedUserId)
                    ->where("follower_user_id", "=", $followerUserId)
                    ->delete();
            } else {
                // if no, follow the user by adding a new follow data
                Follow::create([
                    "follower_user_id" => $followerUserId,
                    "followed_user_id" => $followedUserId
                ]);
            }

            $updatedFollowCount = User::withCount(["followers", "followed_users"])->find($followedUserId);

            return response()->json([
                "followers_count" => $updatedFollowCount->followers_count,
                "followed_users_count" => $updatedFollowCount->followed_users_count
            ]);
        } catch (Exception $e) {
            return response()->json(["error" => $e->getMessage()]);
        }
    }

    public function donationPostsIndex(Request $request)
    {
        $search = $request->get('search');

        $donations = Donation::where("status", "=", 1)
            ->when(filled($search), function ($query) use ($search) {
                $query->where("nama", "LIKE", "%$search%");
            })
            ->orderByDesc("id")
            ->paginate($this->MAX_RESULT_LIMIT);

        return view('social-media.donation-post', compact('donations'));
    }
}
