<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tile; // Tile���f�����g�����߂̐錾��ǉ�

class HomeController extends Controller
{
    /**
     * �A�v���P�[�V�����̃_�b�V���{�[�h��\������
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Tile���f�����g���āAtiles�e�[�u������S�Ẵf�[�^���擾����
        $tiles = Tile::all();

        // �擾�����f�[�^��'tiles'�Ƃ������O�Ńr���[�ɓn��
        return view('dashboard', ['tiles' => $tiles]);
    }
}
