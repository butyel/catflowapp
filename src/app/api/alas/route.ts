import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET() {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  const userId = session.userId;
  const isAdmin = session.userRole === "admin";

  try {
    const where = isAdmin ? {} : { userId };
    const alas = await prisma.ala.findMany({
      where,
      include: { _count: { select: { gatos: true } } },
      orderBy: { nome: "asc" },
    });

    const data = alas.map((a) => ({
      id: a.id,
      nome: a.nome,
      descricao: a.descricao,
      totalGatos: a._count.gatos,
    }));

    return NextResponse.json({ success: true, data });
  } catch (error) {
    console.error("List alas error:", error);
    return NextResponse.json({ success: false, message: "Erro ao buscar alas" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) {
    return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });
  }

  try {
    const body = await request.json();
    const ala = await prisma.ala.create({
      data: {
        userId: session.userId,
        nome: body.nome,
        descricao: body.descricao || null,
      },
    });

    return NextResponse.json({ success: true, data: { id: ala.id }, message: "Ala criada com sucesso!" });
  } catch (error) {
    console.error("Create ala error:", error);
    return NextResponse.json({ success: false, message: "Erro ao criar ala" }, { status: 500 });
  }
}
