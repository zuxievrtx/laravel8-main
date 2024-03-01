<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SiswaController extends Controller
{
    protected $siswa;

    public function __construct(Siswa $siswa)
    {
        // Inject Model Ke Controller
        $this->siswa = $siswa;
    }

    public function index()
    {
        return response()->json(Siswa::all());
    }

    public function store(Request $request)
    {
        // Simpan data siswa ke database

        // validasi data yang masuk agar tidak ada yang kosong
        $rules = [
            'nama' => 'required',
            'alamat' => 'required',
            'nohp' => 'required|min:8',
            'email' => 'required',
            'foto' => 'nullable|image|mimes:jpg,png,jpeg|max:2048'
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }


        $foto = $request->file('foto');

        $uploadedPath = Storage::put('folder-gambar', $foto);

        $result = $this->siswa->create([
            // Data dari request body
            'nama' => $request->post('nama'),
            'alamat' => $request->post('alamat'),
            'email' => $request->post('email'),
            'nohp' => $request->post('nohp'),
            'foto' => $uploadedPath,
        ]);

        $response = [
            'message' => 'Data Siswa Berhasil disimpan',
            'data' => $result
        ];

        return response()->json($response);
    }

    public function show($id)
    {
        // Tampilkan Satu siswa berdasarkan id

        // Cari Siswa Berdasar Id
        $siswa = $this->siswa->findOrFail($id);

        // Jika tidak ditemukan maka kirim response gagal
        if (!$siswa) {
            return response()->json([
                'error' => 'Siswa Tidak Ditemukan'
            ], 404);
        }

        $response = [
            'message' => 'Data Siswa',
            'data' => $siswa
        ];

        return response()->json($response);
    }

    public function update(Request $request, $id)
    {
        // Edit Data Siswa Berdasarkan Id
        $siswa = $this->siswa->findOrFail($id);

        // Jika tidak ditemukan maka kirim response gagal
        if (!$siswa) {
            return response()->json([
                'error' => 'Siswa Tidak Ditemukan',
            ], 404);
        }

        // Periksa apakah ada file foto baru yang diupload
        if ($request->hasFile('foto')) {
            // Hapus foto lama jika ada
            if ($siswa->foto) {
                Storage::delete($siswa->foto);
            }

            // Upload foto baru
            $foto = $request->file('foto');
            $uploadedPath = Storage::put('folder-gambar', $foto);

            // Update path foto di data siswa
            $siswa->foto = $uploadedPath;
        }

        // Update data lainnya
        $siswa->nama = $request->nama;
        $siswa->alamat = $request->alamat;
        $siswa->email = $request->email;
        $siswa->nohp = $request->nohp;

        // Simpan perubahan
        $siswa->save();

        $response = [
            'message' => 'Data Siswa Berhasil Di Update',
            'data' => $siswa,
        ];

        return response()->json($response);
    }

    public function destroy($id)
    {
        // Hapus Data Siswa

        $siswa = $this->siswa->findOrFail($id);

        // Jika tidak ditemukan maka kirim response gagal
        if (!$siswa) {
            return response()->json([
                'error' => 'Siswa Tidak Ditemukan'
            ], 404);
        }
        Storage::delete($siswa->foto);
        $siswa->delete();
        return response()->json(['message' => 'Data Siswa Berhasil dihapus']);
    }
}
