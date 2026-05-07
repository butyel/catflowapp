import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const { searchParams } = new URL(request.url);
  const gatoId = searchParams.get("gato_id") ? parseInt(searchParams.get("gato_id")!) : null;

  try {
    const isAdmin = session.userRole === "admin";
    const where: any = {};

    if (gatoId) {
      where.gatoId = gatoId;
      if (!isAdmin) where.gato = { userId: session.userId };
    } else if (!isAdmin) {
      where.gato = { userId: session.userId };
    }

    const medicamentos = await prisma.medicamento.findMany({
      where,
      include: { gato: { select: { nome: true, id: true } } },
      orderBy: { createdAt: "desc" },
    });

    return NextResponse.json({ success: true, data: medicamentos });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar medicamentos" }, { status: 500 });
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

    const medicamento = await prisma.medicamento.create({
      data: {
        gatoId: body.gatoId,
        nomeMedicamento: body.nomeMedicamento,
        dosagem: body.dosagem,
        horario: body.horario,
        duracaoDias: parseInt(body.duracaoDias) || 0,
        status: "ativo",
      },
    });

    return NextResponse.json({ success: true, data: medicamento, message: "Medicamento cadastrado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao cadastrar medicamento" }, { status: 500 });
  }
}
