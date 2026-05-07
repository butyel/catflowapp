import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET() {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const isAdmin = session.userRole === "admin";

  try {
    const where = isAdmin ? {} : { gato: { userId: session.userId } };

    const adocoes = await prisma.adocao.findMany({
      where,
      include: {
        gato: { select: { id: true, nome: true } },
      },
      orderBy: { dataAdocao: "desc" },
    });

    return NextResponse.json({ success: true, data: adocoes });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar adoções" }, { status: 500 });
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

    const adocao = await prisma.adocao.create({
      data: {
        gatoId: body.gatoId,
        adotanteNome: body.adotanteNome,
        contato: body.contato,
        dataAdocao: new Date(body.dataAdocao),
        observacoes: body.observacoes || null,
      },
    });

    await prisma.gato.update({
      where: { id: body.gatoId },
      data: { status: "adotado" },
    });

    return NextResponse.json({ success: true, data: adocao, message: "Adoção registrada!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao registrar adoção" }, { status: 500 });
  }
}
