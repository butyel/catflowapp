import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const { searchParams } = new URL(request.url);
  const gatoId = searchParams.get("gato_id") ? parseInt(searchParams.get("gato_id")!) : null;

  try {
    const where: any = {};
    const userId = session.userId;
    const isAdmin = session.userRole === "admin";

    if (gatoId) {
      where.gatoId = gatoId;
      if (!isAdmin) where.gato = { userId };
    } else if (!isAdmin) {
      where.gato = { userId };
    }

    const saude = await prisma.saude.findMany({
      where,
      include: { gato: { select: { nome: true, id: true } } },
      orderBy: { dataEvento: "desc" },
    });

    return NextResponse.json({ success: true, data: saude });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar registros de saúde" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();

    const gato = await prisma.gato.findFirst({
      where: {
        id: body.gatoId,
        ...(session.userRole !== "admin" ? { userId: session.userId } : {}),
      },
    });
    if (!gato) return NextResponse.json({ success: false, message: "Gato não encontrado" }, { status: 404 });

    const saude = await prisma.saude.create({
      data: {
        gatoId: body.gatoId,
        tipo: body.tipo,
        descricao: body.descricao,
        dataEvento: new Date(body.dataEvento),
        proximaData: body.proximaData ? new Date(body.proximaData) : null,
      },
    });

    return NextResponse.json({ success: true, data: saude, message: "Registro criado com sucesso!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao criar registro" }, { status: 500 });
  }
}
