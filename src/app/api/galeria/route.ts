import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const { searchParams } = new URL(request.url);
  const gatoId = searchParams.get("gato_id") ? parseInt(searchParams.get("gato_id")!) : null;

  if (!gatoId) return NextResponse.json({ success: false, message: "gato_id é obrigatório" }, { status: 400 });

  try {
    const fotos = await prisma.galeria.findMany({
      where: { gatoId },
      orderBy: { data: "desc" },
    });

    return NextResponse.json({ success: true, data: fotos });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar fotos" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();
    const foto = await prisma.galeria.create({
      data: {
        gatoId: body.gatoId,
        fotoPath: body.fotoPath,
        legenda: body.legenda || null,
        data: new Date(body.data || new Date()),
      },
    });

    return NextResponse.json({ success: true, data: foto, message: "Foto adicionada!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao adicionar foto" }, { status: 500 });
  }
}
