namespace App\Http\Middleware;
use App\Models\ClientVerify;
use Closure;
use Illuminate\Http\Request;

class is_authenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */




     
    public function handle(Request $request, Closure $next)
    {
        $token = $request->header('Authorization');
        $user=ClientVerify::where('remember_me',$token)->first();
        if($user){
            return $next($request);
        }
        else{
        return response('login first');
        }
    }
}
