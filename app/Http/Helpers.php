<?php

use App\Models\Message;
use App\Models\Category;
use App\Models\PostTag;
use App\Models\PostCategory;
use App\Models\Order;
use App\Models\Wishlist;
use App\Models\Shipping;
use App\Models\Cart;
use Illuminate\Support\Str;

// use Auth;
class Helper
{
    public static function base_currency()
    {
        return 'TK';
    }

    public static function currency($amount, $decimals = 2)
    {
        return self::base_currency() . ' ' . number_format((float) $amount, $decimals);
    }

    public static function guestCartToken()
    {
        $token = session('guest_cart_token');

        if (!$token) {
            $token = (string) Str::uuid();
            session(['guest_cart_token' => $token]);
        }

        return $token;
    }

    public static function cartQuery($user_id = '', $withProduct = false)
    {
        $query = $withProduct ? Cart::with('product') : Cart::query();

        if ($user_id !== '') {
            return $query->where('user_id', $user_id);
        }

        if (Auth::check()) {
            return $query->where('user_id', auth()->id());
        }

        return $query->whereNull('user_id')->where('session_id', self::guestCartToken());
    }

    public static function activeCartQuery($user_id = '', $withProduct = false)
    {
        return self::cartQuery($user_id, $withProduct)->whereNull('order_id');
    }

    public static function messageList()
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }
    public static function getAllCategory()
    {
        $category = new Category();
        $menu = $category->getAllParentWithChild();
        return $menu;
    }

    public static function getHeaderCategory()
    {
        $category = new Category();
        // dd($category);
        $menu = $category->getAllParentWithChild();

        if ($menu) {
?>

            <li>
                <a href="javascript:void(0);">Category<i class="ti-angle-down"></i></a>
                <ul class="dropdown border-0 shadow">
                    <?php
                    foreach ($menu as $cat_info) {
                        if ($cat_info->child_cat->count() > 0) {
                    ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a>
                                <ul class="dropdown sub-dropdown border-0 shadow">
                                    <?php
                                    foreach ($cat_info->child_cat as $sub_menu) {
                                    ?>
                                        <li><a href="<?php echo route('product-sub-cat', [$cat_info->slug, $sub_menu->slug]); ?>"><?php echo $sub_menu->title; ?></a></li>
                                    <?php
                                    }
                                    ?>
                                </ul>
                            </li>
                        <?php
                        } else {
                        ?>
                            <li><a href="<?php echo route('product-cat', $cat_info->slug); ?>"><?php echo $cat_info->title; ?></a></li>
                    <?php
                        }
                    }
                    ?>
                </ul>
            </li>
<?php
        }
    }

    public static function productCategoryList($option = 'all')
    {
        if ($option == 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        return Category::has('products')->orderBy('id', 'DESC')->get();
    }

    public static function postTagList($option = 'all')
    {
        if ($option == 'all') {
            return PostTag::orderBy('id', 'desc')->get();
        }

        return PostTag::whereExists(function ($query) {
            $query->selectRaw('1')
                ->from('posts')
                ->where('posts.status', 'active')
                ->whereRaw('FIND_IN_SET(post_tags.title, REPLACE(posts.tags, ", ", ",")) > 0');
        })->orderBy('id', 'desc')->get();
    }

    public static function postCategoryList($option = "all")
    {
        if ($option == 'all') {
            return PostCategory::orderBy('id', 'DESC')->get();
        }
        return PostCategory::has('post')->orderBy('id', 'DESC')->get();
    }
    // Cart Count
    public static function cartCount($user_id = '')
    {
        return self::activeCartQuery($user_id)->sum('quantity');
    }
    // relationship cart with product
    public function product()
    {
        return $this->hasOne('App\Models\Product', 'id', 'product_id');
    }

    public static function getAllProductFromCart($user_id = '')
    {
        return self::activeCartQuery($user_id, true)->get();
    }
    // Total amount cart
    public static function totalCartPrice($user_id = '')
    {
        return self::activeCartQuery($user_id)->sum('amount');
    }
    // Wishlist Count
    public static function wishlistCount($user_id = '')
    {

        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    public static function getAllProductFromWishlist($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get();
        } else {
            return 0;
        }
    }
    public static function totalWishlistPrice($user_id = '')
    {
        if (Auth::check()) {
            if ($user_id == "") $user_id = auth()->user()->id;
            return Wishlist::where('user_id', $user_id)->where('cart_id', null)->sum('amount');
        } else {
            return 0;
        }
    }

    // Total price with shipping and coupon
    public static function grandPrice($id, $user_id)
    {
        $order = Order::find($id);
        if ($order) {
            $shipping_price = (float)$order->shipping->price;
            $order_price = self::orderPrice($id, $user_id);
            return number_format((float)($order_price + $shipping_price), 2, '.', '');
        } else {
            return 0;
        }
    }


    // Admin home
    public static function earningPerMonth()
    {
        $month_data = Order::where('status', 'delivered')->get();
        // return $month_data;
        $price = 0;
        foreach ($month_data as $data) {
            $price = $data->cart_info->sum('price');
        }
        return number_format((float)($price), 2, '.', '');
    }

    public static function shipping()
    {
        return Shipping::orderBy('id', 'DESC')->get();
    }
}



if (!function_exists('generateUniqueSlug')) {
    /**
     * Generate a unique slug for a given title and model.
     *
     * @param string $title
     * @param string $modelClass
     * @return string
     */
    function generateUniqueSlug($title, $modelClass)
    {
        $slug = Str::slug($title);
        $count = $modelClass::where('slug', $slug)->count();

        if ($count > 0) {
            $slug = $slug . '-' . date('ymdis') . '-' . rand(0, 999);
        }

        return $slug;
    }
}

?>
