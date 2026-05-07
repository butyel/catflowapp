import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET() {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const isAdmin = session.userRole === "admin";
  const where: any = isAdmin ? {} : { userId: session.userId };

  try {
    const lembretes = await prisma.lembrete.findMany({
      where,
      orderBy: { dataLembrete: "asc" },
    });

    return NextResponse.json({ success: true, data: lembretes });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar lembretes" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();
    const lembrete = await prisma.lembrete.create({
      data: {
        userId: session.userId,
        titulo: body.titulo,
        descricao: body.descricao || null,
        dataLembrete: new Date(body.dataLembrete),
        status: "pendente",
      },
    });

    return NextResponse.json({ success: true, data: lembrete, message: "Lembrete criado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao criar lembrete" }, { status: 500 });
  }
}
