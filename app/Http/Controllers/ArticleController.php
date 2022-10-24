<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Traits\Batoul;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller
{
    use Batoul;
    public function show($id)
    {
        $article = Article::find($id);
        if (is_null($article)) {
            return $this->sendError(404, 'notfound');
        }
        return $this->sendResponse('articles',$article);
    }
    public function showAll(){
        $article = Article::query()->select('*')->get();
        if (is_null($article)) {
            return $this->sendError(404, 'notfound');
        }
        return $this->sendResponse('articles',$article);
    }
    public function addArticle (Request $request){
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required|string',
            'body' => 'required|string',
            'image' => 'required|image',
        ]);
        if ($validator->fails()){
            return  $this->sendError('add is invalid',$validator->errors());
        }
        $Image = time() .$input['image']->getClientOriginalName();
        $input['image']->move("images", $Image);
        $input['image'] = URL::to('/images') . "/" . $Image;
        $article =new Article();
        $user_id =Auth::id();
        $article->fill([
            'name' =>$input['name'],
            'body' => $input['body'],
            'image'=>$input['image'],
            'admin_id'=>$user_id
        ]);
        $article->save();
        return $this->sendResponse('success','article added successfully');
    }
    public function destroy($id)
    {
        $article = Article::find($id);
        if (!$article)
            return $this->sendError('not found', 'not found');
        unlink(substr($article['image'], strlen(URL::to('/')) + 1));
        $article->delete();
        return $this->sendResponse('success','article delete successfully');
    }
}
