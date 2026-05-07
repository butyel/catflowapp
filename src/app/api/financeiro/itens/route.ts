import { NextResponse } from "next/server";
import { getSession } from "@/lib/auth";
import { prisma } from "@/lib/db";

export async function GET() {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  const isAdmin = session.userRole === "admin";
  const where = isAdmin ? {} : { userId: session.userId };

  try {
    const itens = await prisma.financeiroItem.findMany({
      where,
      orderBy: { nome: "asc" },
    });

    const data = itens.map((i) => ({
      ...i,
      precoUnitario: Number(i.precoUnitario),
    }));

    return NextResponse.json({ success: true, data });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao buscar itens" }, { status: 500 });
  }
}

export async function POST(request: Request) {
  const session = await getSession();
  if (!session) return NextResponse.json({ success: false, message: "Não autenticado" }, { status: 401 });

  try {
    const body = await request.json();
    const item = await prisma.financeiroItem.create({
      data: {
        userId: session.userId,
        nome: body.nome,
        categoria: body.categoria,
        precoUnitario: parseFloat(body.precoUnitario) || 0,
        unidade: body.unidade || "un",
      },
    });

    return NextResponse.json({ success: true, data: item, message: "Item criado!" });
  } catch (error) {
    return NextResponse.json({ success: false, message: "Erro ao criar item" }, { status: 500 });
  }
}
